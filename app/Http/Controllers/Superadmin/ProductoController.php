<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\LogAuditoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = Producto::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre',      'like', "%{$search}%")
                  ->orWhere('codigoBarras','like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        if ($cat = $request->input('categoria')) {
            $query->where('categoria', $cat);
        }

        if ($request->input('stock') === 'bajo') {
            $query->whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo');
        } elseif ($request->input('stock') === 'agotado') {
            $query->where('stockActual', '<=', 0);
        }

        if ($request->input('activo') !== null && $request->input('activo') !== '') {
            $query->where('activo', (bool) $request->input('activo'));
        }

        $productos    = $query->paginate(12)->withQueryString();

        // Combina categorías registradas + las ya usadas en productos
        $categoriasRegistradas = Categoria::orderBy('nombre')->pluck('nombre');
        $categoriasEnUso       = Producto::whereNotNull('categoria')->distinct()->pluck('categoria');
        $categorias            = $categoriasRegistradas->merge($categoriasEnUso)->unique()->sort()->values();

        $stats = [
            'total'   => Producto::count(),
            'activos' => Producto::where('activo', true)->count(),
            'bajo'    => Producto::whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo')->count(),
            'agotado' => Producto::where('stockActual', '<=', 0)->count(),
        ];

        return view('superadmin.productos.index', compact('productos', 'categorias', 'stats'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string|max:1000',
            'precio'            => 'required|numeric|min:0',
            'unidad'            => 'required|string|max:50',
            'stockActual'       => 'required|integer|min:0',
            'stockMinimo'       => 'nullable|integer|min:0',
            'categoria'         => 'required|string|max:100',
            'codigoBarras'      => 'nullable|string|max:255',
            'diasPerecederoMax' => 'nullable|integer|min:0',
            'imagen'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $data['activo'] = $request->input('activo') == '1' || $request->input('activo') === true;

        Producto::create($data);

        LogAuditoria::registrar('Productos', 'Creación', "Se creó el producto: {$data['nombre']}");

        return redirect()->route('superadmin.productos.index')
                         ->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string|max:1000',
            'precio'            => 'required|numeric|min:0',
            'unidad'            => 'required|string|max:50',
            'stockActual'       => 'required|integer|min:0',
            'stockMinimo'       => 'nullable|integer|min:0',
            'categoria'         => 'required|string|max:100',
            'codigoBarras'      => 'nullable|string|max:255',
            'diasPerecederoMax' => 'nullable|integer|min:0',
            'imagen'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $data['activo'] = $request->input('activo') == '1' || $request->input('activo') === true;
        $producto->update($data);

        LogAuditoria::registrar('Productos', 'Actualización', "Se actualizó el producto: {$producto->nombre}");

        return redirect()->route('superadmin.productos.index')
                         ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $this->soloSuperadmin();
        if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
        $producto->delete();
        LogAuditoria::registrar('Productos', 'Eliminación', "Se eliminó el producto: {$producto->nombre}");
        return redirect()->route('superadmin.productos.index')
                         ->with('success', 'Producto eliminado correctamente.');
    }
}
