<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function show(Categoria $categoria, Request $request)
    {
        $this->soloSuperadmin();

        $query = Producto::where('categoria', $categoria->nombre);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($stock = $request->input('stock')) {
            if ($stock === 'agotado') {
                $query->where('stockActual', '<=', 0);
            } elseif ($stock === 'bajo') {
                $query->whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo')->where('stockActual', '>', 0);
            }
        }

        $productos = $query->paginate(10)->withQueryString();

        $stats = [
            'total'   => Producto::where('categoria', $categoria->nombre)->count(),
            'activos' => Producto::where('categoria', $categoria->nombre)->where('activo', true)->count(),
            'bajo'    => Producto::where('categoria', $categoria->nombre)->whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo')->where('stockActual', '>', 0)->count(),
            'agotado' => Producto::where('categoria', $categoria->nombre)->where('stockActual', '<=', 0)->count(),
        ];

        return view('superadmin.categorias.show', compact('categoria', 'productos', 'stats'));
    }

    public function index(Request $request)    {
        $this->soloSuperadmin();

        $query = Categoria::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($request->input('estado') !== null && $request->input('estado') !== '') {
            $query->where('activo', (bool) $request->input('estado'));
        }

        $categorias = $query->get();
        $total      = Categoria::count();

        // Contar productos por categoría en una sola query
        $productosCount = Producto::selectRaw('categoria, COUNT(*) as total')
            ->whereNotNull('categoria')
            ->groupBy('categoria')
            ->pluck('total', 'categoria');

        return view('superadmin.categorias.index', compact('categorias', 'total', 'productosCount'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categoria,nombre',
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:10',
            'activo'      => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['icono']  = $data['icono'] ?: '📦';

        Categoria::create($data);

        return redirect()->route('superadmin.categorias.index')
                         ->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', Rule::unique('categoria', 'nombre')->ignore($categoria->id)],
            'descripcion' => 'nullable|string|max:500',
            'icono'       => 'nullable|string|max:10',
            'activo'      => 'boolean',
        ]);

        $nombreAnterior = $categoria->nombre;
        $data['activo'] = $request->boolean('activo');
        $data['icono']  = $data['icono'] ?: '📦';

        $categoria->update($data);

        // Si cambió el nombre, actualizar los productos que usaban la categoría anterior
        if ($nombreAnterior !== $data['nombre']) {
            Producto::where('categoria', $nombreAnterior)->update(['categoria' => $data['nombre']]);
        }

        return redirect()->route('superadmin.categorias.index')
                         ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        $this->soloSuperadmin();
        $categoria->delete();

        return redirect()->route('superadmin.categorias.index')
                         ->with('success', 'Categoría eliminada.');
    }

    public function toggleActivo(Categoria $categoria)
    {
        $this->soloSuperadmin();
        $categoria->update(['activo' => !$categoria->activo]);
        return back()->with('success', 'Estado de categoría actualizado.');
    }
}
