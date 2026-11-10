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

        $query = Producto::with([
            'proveedor',
            'lotes' => fn($q) => $q->where('cantidad_disponible', '>', 0)->orderBy('fecha_vencimiento'),
        ])->where('activo', true);

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
            $query->whereNotNull('stockMinimo')
                  ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) <= stockMinimo')
                  ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) > 0');
        } elseif ($request->input('stock') === 'agotado') {
            $query->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0));
        } elseif ($request->input('stock') === 'vencido') {
            $query->whereHas('lotes', fn($q) => $q
                ->where('cantidad_disponible', '>', 0)
                ->whereNotNull('fecha_vencimiento')
                ->where('fecha_vencimiento', '<', now()->startOfDay())
            );
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

    // ── Buscar producto por código de barras o ID (AJAX) ───────

    public function buscarPorCodigo(string $codigo)
    {
        Gate::authorize('viewAny', Producto::class);

        $codigo = trim($codigo);

        $producto = Producto::with(['proveedor', 'lotes' => function($q) {
            $q->where('cantidad_disponible', '>', 0)
              ->orderByRaw('ISNULL(fecha_vencimiento), fecha_vencimiento ASC');
        }])->where(function($q) use ($codigo) {
            $q->where('codigoBarras', $codigo)
              ->orWhere('id', $codigo);
        })->where('activo', true)->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún producto con el código: ' . $codigo,
            ], 404);
        }

        $stockTotal = $producto->stock_calculado;
        $stockVencido = $producto->stock_vencido;

        $lotesData = $producto->lotes->map(function($lote) {
            return [
                'id' => $lote->id,
                'cantidad_disponible' => $lote->cantidad_disponible,
                'fecha_vencimiento' => $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('Y-m-d') : null,
                'fecha_vencimiento_formateada' => $lote->fecha_vencimiento ? $lote->fecha_vencimiento->locale('es')->isoFormat('D MMM YYYY') : 'Sin vencimiento',
                'vencido' => $lote->vencido,
                'proximo_vencer' => $lote->proximo_vencer,
            ];
        });

        return response()->json([
            'success' => true,
            'producto' => [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'categoria' => $producto->categoria,
                'unidad' => $producto->unidad,
                'precio' => (float) $producto->precio,
                'precio_formateado' => '$' . number_format($producto->precio, 0, ',', '.'),
                'codigoBarras' => $producto->codigoBarras,
                'proveedor' => $producto->proveedor?->nombre ?? 'Sin proveedor',
                'stock_total' => $stockTotal,
                'stock_vencido' => $stockVencido,
                'lotes' => $lotesData,
            ],
        ]);
    }

    // ── Generar código de barras único para un producto ────────

    public function generarCodigoBarras(Producto $producto)
    {
        Gate::authorize('registrarMovimiento', $producto);

        if ($producto->codigoBarras) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'codigoBarras' => $producto->codigoBarras,
                    'message' => 'El producto ya cuenta con código: ' . $producto->codigoBarras,
                ]);
            }
            return back()->with('success', 'El producto ya cuenta con el código: ' . $producto->codigoBarras);
        }

        $nuevoCodigo = Producto::generarCodigoBarras();
        $producto->update(['codigoBarras' => $nuevoCodigo]);

        LogAuditoria::registrar('Inventario', 'Actualización',
            "Se generó código de barras {$nuevoCodigo} para el producto {$producto->nombre}");

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'codigoBarras' => $nuevoCodigo,
                'message' => 'Código de barras generado con éxito: ' . $nuevoCodigo,
            ]);
        }

        return back()->with('success', 'Código de barras generado con éxito: ' . $nuevoCodigo);
    }

    // ── Dar de baja un lote vencido directamente ────────────────

    public function darDeBajaLote(Request $request, Lote $lote)
    {
        $producto = $lote->producto;
        Gate::authorize('registrarMovimiento', $producto);

        $messages = [
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad debe ser mínimo 1.',
            'motivo.max'        => 'El detalle no puede superar 200 caracteres.',
        ];

        $data = $request->validate([
            'cantidad' => 'nullable|integer|min:1|max:' . max(1, $lote->cantidad_disponible),
            'motivo'   => 'nullable|string|max:200',
        ], $messages);

        $cantidad = !empty($data['cantidad']) ? (int) $data['cantidad'] : $lote->cantidad_disponible;

        if ($cantidad <= 0 || $lote->cantidad_disponible <= 0) {
            return back()->withErrors(['lote' => 'El lote ya no cuenta con unidades disponibles para dar de baja.']);
        }

        if ($cantidad > $lote->cantidad_disponible) {
            return back()->withErrors(['cantidad' => "Solo hay {$lote->cantidad_disponible} unidades disponibles en este lote."]);
        }

        $lote->decrement('cantidad_disponible', $cantidad);

        $detalle = !empty($data['motivo']) ? "Baja por vencimiento: {$data['motivo']}" : "Baja por vencimiento (Lote #{$lote->id})";
        $motivoTexto = mb_substr($detalle, 0, 250);

        MovimientoInventario::create([
            'producto_id'     => $producto->id,
            'lote_id'         => $lote->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'salida',
            'cantidad'        => $cantidad,
            'motivo'          => $motivoTexto,
            'fechaMovimiento' => now(),
        ]);

        LogAuditoria::registrar('Inventario', 'Actualización',
            "Baja de {$cantidad} unidades vencidas de {$producto->nombre} (Lote #{$lote->id})");

        \App\Services\OrdenPedidoService::verificarYGenerarAutomaticamente($producto, auth()->id());

        return back()->with('success', "Se dieron de baja {$cantidad} {$producto->unidad} vencidas del lote #{$lote->id} correctamente.");
    }

    // ── Registrar ENTRADA (crea un lote nuevo) ─────────────────

    public function storeEntrada(Request $request, Producto $producto)
    {
        Gate::authorize('registrarMovimiento', $producto);

        $messages = [
            'cantidad.required'                => 'La cantidad es obligatoria.',
            'cantidad.integer'                 => 'La cantidad debe ser un número entero.',
            'cantidad.min'                     => 'La cantidad debe ser mayor a 0.',
            'cantidad.max'                     => 'La cantidad no puede superar 9.999.999 unidades.',
            'fecha_entrada.required'           => 'La fecha de entrada es obligatoria.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser anterior a la fecha de entrada.',
            'notas.max'                        => 'Las notas no pueden superar los 500 caracteres.',
        ];

        $data = $request->validate([
            'cantidad'          => 'required|integer|min:1|max:9999999',
            'fecha_entrada'     => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_entrada',
            'notas'             => 'nullable|string|max:500',
        ], $messages);

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

    // ── Registrar SALIDA (Estándar o por Lote / Vencimiento) ───

    public function storeSalida(Request $request, Producto $producto)
    {
        Gate::authorize('registrarMovimiento', $producto);

        $messages = [
            'cantidad.required'  => 'La cantidad es obligatoria.',
            'cantidad.integer'   => 'La cantidad debe ser un número entero.',
            'cantidad.min'       => 'La cantidad debe ser mayor a 0.',
            'cantidad.max'       => 'La cantidad no puede superar 9.999.999 unidades.',
            'motivo.required'    => 'El motivo es obligatorio.',
            'motivo_detalle.max' => 'El detalle no puede superar los 200 caracteres.',
        ];

        $data = $request->validate([
            'cantidad'       => 'required|integer|min:1|max:9999999',
            'motivo'         => 'required|string|in:venta,vencido,merma,consumo_interno,ajuste,otro',
            'motivo_detalle' => 'nullable|string|max:200',
            'lote_id'        => 'nullable|exists:lotes,id',
        ], $messages);

        try {
            $resultado = $this->procesarSalida(
                $producto,
                (int) $data['cantidad'],
                $data['motivo'],
                $data['motivo_detalle'] ?? null,
                !empty($data['lote_id']) ? (int) $data['lote_id'] : null
            );

            return redirect()->route('superadmin.inventario.show', $producto)
                ->with('success', "Salida de {$resultado['cantidad']} {$producto->unidad} registrada correctamente.");
        } catch (\Exception $e) {
            return back()->withErrors(['cantidad' => $e->getMessage()])->withInput();
        }
    }

    // ── Salida Rápida (para escáner de código de barras) ────────

    public function storeSalidaRapida(Request $request)
    {
        $data = $request->validate([
            'producto_id'    => 'required|exists:producto,id',
            'cantidad'       => 'required|integer|min:1|max:9999999',
            'motivo'         => 'required|string|in:venta,vencido,merma,consumo_interno,ajuste,otro',
            'motivo_detalle' => 'nullable|string|max:200',
            'lote_id'        => 'nullable|exists:lotes,id',
        ], [
            'producto_id.required' => 'Debe seleccionar un producto.',
            'cantidad.required'    => 'La cantidad es obligatoria.',
            'cantidad.integer'     => 'La cantidad debe ser un número entero.',
            'cantidad.min'         => 'La cantidad debe ser mayor a 0.',
            'motivo.required'      => 'El motivo es obligatorio.',
        ]);

        $producto = Producto::findOrFail($data['producto_id']);
        Gate::authorize('registrarMovimiento', $producto);

        try {
            $resultado = $this->procesarSalida(
                $producto,
                (int) $data['cantidad'],
                $data['motivo'],
                $data['motivo_detalle'] ?? null,
                !empty($data['lote_id']) ? (int) $data['lote_id'] : null
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Salida de {$resultado['cantidad']} {$producto->unidad} de {$producto->nombre} registrada correctamente.",
                    'nuevo_stock' => $resultado['nuevo_stock'],
                ]);
            }

            return back()->with('success', "Salida de {$resultado['cantidad']} {$producto->unidad} de {$producto->nombre} registrada correctamente.");
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['cantidad' => $e->getMessage()])->withInput();
        }
    }

    // ── Lógica común de procesamiento de salida ───────────────

    protected function procesarSalida(Producto $producto, int $cantidad, string $motivoTipo, ?string $motivoDetalle, ?int $loteId = null): array
    {
        $motivoEtiqueta = match($motivoTipo) {
            'vencido'         => 'Producto Vencido / Desecho',
            'merma'           => 'Merma / Avería',
            'venta'           => 'Venta',
            'consumo_interno' => 'Consumo Interno',
            'ajuste'          => 'Ajuste de Inventario',
            default           => 'Salida de Inventario',
        };
        $motivoTexto = mb_substr($motivoDetalle ? "{$motivoEtiqueta} - {$motivoDetalle}" : $motivoEtiqueta, 0, 250);

        // 1. Si se especificó un lote puntual:
        if ($loteId) {
            $lote = $producto->lotes()->where('id', $loteId)->first();
            if (!$lote) {
                throw new \Exception("El lote seleccionado no pertenece a este producto.");
            }
            if ($lote->cantidad_disponible < $cantidad) {
                throw new \Exception("El lote #{$lote->id} solo cuenta con {$lote->cantidad_disponible} unidades disponibles.");
            }

            $lote->decrement('cantidad_disponible', $cantidad);

            MovimientoInventario::create([
                'producto_id'     => $producto->id,
                'lote_id'         => $lote->id,
                'user_id'         => auth()->id(),
                'tipo'            => 'salida',
                'cantidad'        => $cantidad,
                'motivo'          => $motivoTexto,
                'fechaMovimiento' => now(),
            ]);

            LogAuditoria::registrar('Inventario', 'Actualización',
                "Salida de {$cantidad} unidades de {$producto->nombre} desde lote #{$lote->id} ({$motivoTexto})");

            return [
                'cantidad' => $cantidad,
                'nuevo_stock' => $producto->fresh()->stock_calculado,
            ];
        }

        // 2. Si es salida general sin lote fijado:
        $stockTotal = $producto->lotes()->where('cantidad_disponible', '>', 0)->sum('cantidad_disponible');
        if ($cantidad > $stockTotal) {
            throw new \Exception("Stock insuficiente. Disponible total: {$stockTotal} unidades.");
        }

        // Si el motivo es vencido, priorizar lotes vencidos primero
        $queryLotes = $producto->lotes()->where('cantidad_disponible', '>', 0);
        if ($motivoTipo === 'vencido') {
            $lotes = $queryLotes->orderByRaw("CASE WHEN fecha_vencimiento IS NOT NULL AND fecha_vencimiento < ? THEN 0 ELSE 1 END, ISNULL(fecha_vencimiento), fecha_vencimiento ASC", [now()->startOfDay()])->get();
        } else {
            $lotes = $queryLotes->orderByRaw('ISNULL(fecha_vencimiento), fecha_vencimiento ASC')->get();
        }

        $cantidadPendiente = $cantidad;

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
                'motivo'          => $motivoTexto,
                'fechaMovimiento' => now(),
            ]);

            $cantidadPendiente -= $descuento;
        }

        LogAuditoria::registrar('Inventario', 'Actualización',
            "Salida de {$cantidad} unidades de {$producto->nombre} ({$motivoTexto})");

        \App\Services\OrdenPedidoService::verificarYGenerarAutomaticamente($producto, auth()->id());

        return [
            'cantidad' => $cantidad,
            'nuevo_stock' => $producto->fresh()->stock_calculado,
        ];
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
