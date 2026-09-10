<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Http\Request;

class VentaController extends Controller
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

        // Ventas que contienen productos de este proveedor
        $query = Venta::with(['items.producto'])
            ->whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->orderByDesc('fechaVenta');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cliente', 'like', "%{$search}%");
            });
        }

        if ($mes = $request->input('mes')) {
            $query->whereMonth('fechaVenta', $mes);
        }

        $ventas = $query->paginate(10)->withQueryString();

        $hoy = now();
        $stats = [
            'total_mes'    => $this->countVentasMes($proveedorId, $hoy->month, $hoy->year),
            'ingresos_mes' => $this->sumIngresosMes($proveedorId, $hoy->month, $hoy->year),
            'total_hoy'    => $this->countVentasHoy($proveedorId),
        ];

        return view('proveedor.ventas.index', compact('ventas', 'stats'));
    }

    private function countVentasMes(int $proveedorId, int $mes, int $anio): int
    {
        return Venta::whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereMonth('fechaVenta', $mes)->whereYear('fechaVenta', $anio)->count();
    }

    private function sumIngresosMes(int $proveedorId, int $mes, int $anio): float
    {
        return VentaItem::whereHas('producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereHas('venta', fn($q) => $q->whereMonth('fechaVenta', $mes)->whereYear('fechaVenta', $anio))
            ->sum('subtotal');
    }

    private function countVentasHoy(int $proveedorId): int
    {
        return Venta::whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereDate('fechaVenta', now()->toDateString())->count();
    }
}
