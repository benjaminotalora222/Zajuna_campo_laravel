<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Consignacion;
use App\Models\Lote;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ConsignacionController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $tab = $request->input('tab', 'catalogo'); // 'catalogo' | 'consignacion'

        // ── TAB CATÁLOGO: productos con lotes ────────────────────────
        $queryProductos = \App\Models\Producto::with([
            'proveedor',
            'lotes' => fn($q) => $q->where('cantidad_disponible', '>', 0)->orderBy('fecha_vencimiento'),
        ])->where('activo', true);

        if ($search = $request->input('search')) {
            $queryProductos->where(fn($q) => $q
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('codigoBarras', 'like', "%{$search}%")
            );
        }
        if ($prv = $request->input('proveedor_id')) {
            $queryProductos->where('proveedor_id', $prv);
        }

        $productos = $queryProductos->paginate(10, ['*'], 'pag_prod')->withQueryString();

        // ── TAB CONSIGNACIÓN ─────────────────────────────────────────
        $queryConsig = Consignacion::with('proveedor')->latest();

        if ($search = $request->input('search')) {
            $queryConsig->where(fn($q) => $q
                ->where('nombre_producto', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$search}%"))
            );
        }
        if ($estado = $request->input('estado')) {
            $queryConsig->where('estado', $estado);
        }
        if ($prv = $request->input('proveedor_id')) {
            $queryConsig->where('proveedor_id', $prv);
        }

        $consignaciones = $queryConsig->paginate(10, ['*'], 'pag_consig')->withQueryString();

        $proveedores = Proveedor::orderBy('nombre')->get();

        $stats = [
            'total_productos'  => \App\Models\Producto::where('activo', true)->count(),
            'stock_total'      => \App\Models\Lote::sum('cantidad_disponible'),
            'proximo_vencer'   => \App\Models\Lote::where('cantidad_disponible', '>', 0)
                                    ->whereNotNull('fecha_vencimiento')
                                    ->whereRaw('DATEDIFF(fecha_vencimiento, CURDATE()) <= 30')
                                    ->whereRaw('fecha_vencimiento >= CURDATE()')
                                    ->count(),
            'proveedores'      => Proveedor::where('estado', true)->count(),
        ];

        return view('superadmin.consignacion.index', compact(
            'tab', 'productos', 'consignaciones', 'proveedores', 'stats'
        ));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'proveedor_id'      => 'required|exists:proveedor,id',
            'nombre_producto'   => 'required|string|max:255',
            'sku'               => 'nullable|string|max:100',
            'unidad'            => 'nullable|string|max:50',
            'stock_disponible'  => 'required|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'notas'             => 'nullable|string|max:500',
        ]);

        $c = new Consignacion($data);
        $c->recalcularEstado();
        $c->save();

        return redirect()->route('superadmin.consignacion.index')
                         ->with('success', 'Ingreso de consignación registrado correctamente.');
    }

    public function update(Request $request, Consignacion $consignacion)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'proveedor_id'      => 'required|exists:proveedor,id',
            'nombre_producto'   => 'required|string|max:255',
            'sku'               => 'nullable|string|max:100',
            'unidad'            => 'nullable|string|max:50',
            'stock_disponible'  => 'required|integer|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'notas'             => 'nullable|string|max:500',
        ]);

        $consignacion->fill($data);
        $consignacion->recalcularEstado();
        $consignacion->save();

        return redirect()->route('superadmin.consignacion.index')
                         ->with('success', 'Consignación actualizada correctamente.');
    }

    public function destroy(Consignacion $consignacion)
    {
        $this->soloSuperadmin();
        $consignacion->delete();

        return redirect()->route('superadmin.consignacion.index')
                         ->with('success', 'Consignación eliminada correctamente.');
    }
}
