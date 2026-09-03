<?php

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Consignacion;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ConsignacionController extends Controller
{
    private function soloOperativo()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloOperativo();

        $query = Consignacion::with('proveedor')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_producto', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($proveedorId = $request->input('proveedor_id')) {
            $query->where('proveedor_id', $proveedorId);
        }

        $consignaciones = $query->paginate(10)->withQueryString();
        $proveedores    = Proveedor::where('estado', true)->orderBy('nombre')->get();

        $stats = [
            'total_productos' => Consignacion::count(),
            'stock_total'     => Consignacion::sum('stock_disponible'),
            'proximo_vencer'  => Consignacion::where('estado', 'proximo_vencer')->count(),
            'proveedores'     => Proveedor::where('estado', true)->count(),
        ];

        return view('operativo.consignacion.index', compact('consignaciones', 'proveedores', 'stats'));
    }

    public function store(Request $request)
    {
        $this->soloOperativo();

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

        return redirect()->route('operativo.consignacion.index')
                         ->with('success', 'Ingreso de consignación registrado correctamente.');
    }

    public function update(Request $request, Consignacion $consignacion)
    {
        $this->soloOperativo();

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

        return redirect()->route('operativo.consignacion.index')
                         ->with('success', 'Consignación actualizada correctamente.');
    }

    public function destroy(Consignacion $consignacion)
    {
        $this->soloOperativo();
        $consignacion->delete();

        return redirect()->route('operativo.consignacion.index')
                         ->with('success', 'Consignación eliminada correctamente.');
    }
}
