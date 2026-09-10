<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    private function soloProveedor(): void
    {
        if (auth()->user()->role_id !== 3) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloProveedor();

        $proveedorId = auth()->user()->proveedor_id;

        if (! $proveedorId) {
            abort(403, 'Tu cuenta no tiene un proveedor asociado.');
        }

        $query = Producto::with([
                'lotes' => fn($q) => $q->orderBy('fecha_vencimiento'),
            ])
            ->where('proveedor_id', $proveedorId)
            ->where('activo', true);

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('codigoBarras', 'like', "%{$search}%")
            );
        }

        if ($estado = $request->input('estado')) {
            $query = match($estado) {
                'disponible'     => $query->whereHas('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0)),
                'proximo_vencer' => $query->whereHas('lotes', fn($q) =>
                                        $q->where('cantidad_disponible', '>', 0)
                                          ->whereNotNull('fecha_vencimiento')
                                          ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                                          ->whereRaw('fecha_vencimiento > NOW()')
                                    ),
                'agotado'        => $query->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0)),
                default          => $query,
            };
        }

        $productos = $query->paginate(10)->withQueryString();

        // Stock total sumando cantidad_disponible de todos los lotes del proveedor
        $stockTotal = Producto::where('proveedor_id', $proveedorId)
            ->where('activo', true)
            ->withSum('lotes as stock', 'cantidad_disponible')
            ->get()
            ->sum('stock');

        // Próximos a vencer: lotes con stock > 0 que vencen en ≤ 30 días
        $proximoVencer = Producto::where('proveedor_id', $proveedorId)
            ->whereHas('lotes', fn($q) =>
                $q->where('cantidad_disponible', '>', 0)
                  ->whereNotNull('fecha_vencimiento')
                  ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                  ->whereRaw('fecha_vencimiento > NOW()')
            )
            ->count();

        $stats = [
            'total'          => Producto::where('proveedor_id', $proveedorId)->where('activo', true)->count(),
            'stock_total'    => (int) $stockTotal,
            'proximo_vencer' => $proximoVencer,
        ];

        return view('proveedor.inventario.index', compact('productos', 'stats'));
    }
}
