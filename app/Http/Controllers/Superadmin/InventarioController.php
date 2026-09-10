<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\LogAuditoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventarioController extends Controller
{
    // ── Listado ────────────────────────────────────────────────

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Producto::class);

        $query = Producto::with(['proveedor', 'lotes' => fn($q) => $q->where('cantidad_disponible', '>', 0)->orderBy('fecha_vencimiento')])
            ->where('activo', true);

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('codigoBarras', 'like', "%{$search}%")
            );
        }

        if ($prv = $request->input('proveedor_id')) {
            $query->where('proveedor_id', $prv);
        }

        if ($request->input('stock') === 'bajo') {
            $query->whereHas('lotes', fn($q) => $q, '<', 'stockMinimo');
        } elseif ($request->input('stock') === 'agotado') {
            $query->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0));
        }

        $productos   = $query->paginate(10)->withQueryString();
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        return view('superadmin.inventario.index', compact('productos', 'proveedores'));
    }

    // ── Detalle del producto (lotes + movimientos) ─────────────

    public function show(Producto $producto)
    {
        Gate::authorize('view', $producto);

        $lotes = $producto->lotes()
            ->orderBy('fecha_vencimiento')
            ->paginate(10, ['*'], 'lotes_page');

        $movimientos = $producto->movimientos()
            ->with(['usuario', 'lote'])
            ->orderByDesc('fechaMovimiento')
            ->paginate(10, ['*'], 'mov_page');

        return view('superadmin.inventario.show', compact('producto', 'lotes', 'movimientos'));
    }

    // ── Registrar ENTRADA (crea un lote nuevo) ─────────────────

    public function storeEntrada(Request $request, Producto $producto)
    {
        Gate::authorize('registrarMovimiento', $producto);

        $data = $request->validate([
            'cantidad'          => 'required|integer|min:1',
            'fecha_entrada'     => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_entrada',
            'notas'             => 'nullable|string|max:500',
        ]);

        $lote = Lote::create([
            'producto_id'        => $producto->id,
            'cantidad_inicial'   => $data['cantidad'],
            'cantidad_disponible'=> $data['cantidad'],
            'fecha_entrada'      => $data['fecha_entrada'],
            'fecha_vencimiento'  => $data['fecha_vencimiento'] ?? null,
            'notas'              => $data['notas'] ?? null,
        ]);

        MovimientoInventario::create([
            'producto_id'      => $producto->id,
            'lote_id'          => $lote->id,
            'user_id'          => auth()->id(),
            'tipo'             => 'entrada',
            'cantidad'         => $data['cantidad'],
            'motivo'           => $data['notas'] ?? 'Entrada de lote',
            'fechaMovimiento'  => now(),
        ]);

        LogAuditoria::registrar('Inventario', 'Creación',
            "Entrada de {$data['cantidad']} unidades de {$producto->nombre} (lote #{$lote->id})");

        return redirect()->route('superadmin.inventario.show', $producto)
            ->with('success', 'Entrada registrada correctamente.');
    }

    // ── Registrar SALIDA (FEFO) ────────────────────────────────

    public function storeSalida(Request $request, Producto $producto)
    {
        Gate::authorize('registrarMovimiento', $producto);

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'motivo'   => 'nullable|string|max:500',
        ]);

        $cantidadPendiente = $data['cantidad'];
        $stockTotal        = $producto->lotes()->where('cantidad_disponible', '>', 0)->sum('cantidad_disponible');

        if ($cantidadPendiente > $stockTotal) {
            return back()->withErrors(['cantidad' => "Stock insuficiente. Disponible: {$stockTotal}"]);
        }

        // FEFO: descontar primero del lote más próximo a vencer
        $lotes = $producto->lotes()
            ->where('cantidad_disponible', '>', 0)
            ->orderByRaw('ISNULL(fecha_vencimiento), fecha_vencimiento ASC')
            ->get();

        foreach ($lotes as $lote) {
            if ($cantidadPendiente <= 0) break;

            $descuento = min($lote->cantidad_disponible, $cantidadPendiente);
            $lote->decrement('cantidad_disponible', $descuento);

            MovimientoInventario::create([
                'producto_id'     => $producto->id,
                'lote_id'         => $lote->id,
                'user_id'         => auth()->id(),
                'tipo'            => 'salida',
                'cantidad'        => $descuento,
                'motivo'          => $data['motivo'] ?? 'Salida de inventario',
                'fechaMovimiento' => now(),
            ]);

            $cantidadPendiente -= $descuento;
        }

        LogAuditoria::registrar('Inventario', 'Actualización',
            "Salida de {$data['cantidad']} unidades de {$producto->nombre} (FEFO)");

        return redirect()->route('superadmin.inventario.show', $producto)
            ->with('success', 'Salida registrada correctamente.');
    }

    // ── Editar un movimiento (corregir cantidad/motivo) ────────

    public function updateMovimiento(Request $request, MovimientoInventario $movimiento)
    {
        Gate::authorize('registrarMovimiento', $movimiento->producto);

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'motivo'   => 'nullable|string|max:500',
        ]);

        $diferencia = $data['cantidad'] - $movimiento->cantidad;

        // Ajustar lote si hay uno asociado
        if ($movimiento->lote_id) {
            $lote = $movimiento->lote;
            if ($movimiento->tipo === 'entrada') {
                $lote->increment('cantidad_disponible', $diferencia);
                $lote->increment('cantidad_inicial', $diferencia);
            } else {
                $lote->decrement('cantidad_disponible', $diferencia);
            }
        }

        $movimiento->update([
            'cantidad' => $data['cantidad'],
            'motivo'   => $data['motivo'] ?? $movimiento->motivo,
        ]);

        LogAuditoria::registrar('Inventario', 'Actualización',
            "Movimiento #{$movimiento->id} corregido a {$data['cantidad']} unidades");

        return back()->with('success', 'Movimiento actualizado.');
    }
}
