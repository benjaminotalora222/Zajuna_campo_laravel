<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\LogAuditoria;
use App\Models\Producto;
use App\Models\Proveedor;
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
            // Stock bajo: productos cuyo stock calculado desde lotes <= stockMinimo
            $query->whereHas('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0))
                  ->whereNotNull('stockMinimo')
                  ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) <= stockMinimo');
        } elseif ($request->input('stock') === 'agotado') {
            $query->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0));
        }

        if ($request->input('activo') !== null && $request->input('activo') !== '') {
            $query->where('activo', (bool) $request->input('activo'));
        }

        $productos    = $query->paginate(10)->withQueryString();

        // Combina categorías registradas + las ya usadas en productos
        $categoriasRegistradas = Categoria::orderBy('nombre')->pluck('nombre');
        $categoriasEnUso       = Producto::whereNotNull('categoria')->distinct()->pluck('categoria');
        $categorias            = $categoriasRegistradas->merge($categoriasEnUso)->unique()->sort()->values();

        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        $stats = [
            'total'   => Producto::count(),
            'activos' => Producto::where('activo', true)->count(),
            'bajo'    => Producto::whereNotNull('stockMinimo')
                            ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) <= stockMinimo')
                            ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) > 0')
                            ->count(),
            'agotado' => Producto::whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0))->count(),
        ];

        return view('superadmin.productos.index', compact('productos', 'categorias', 'stats', 'proveedores'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $messages = [
            'nombre.required'           => 'El nombre del producto es obligatorio.',
            'nombre.min'                => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios (no se permiten números).',
            'descripcion.max'           => 'La descripción no puede superar los 1000 caracteres.',
            'precio.required'           => 'El precio es obligatorio.',
            'precio.numeric'            => 'El precio debe ser un número válido.',
            'precio.min'                => 'El precio no puede ser negativo.',
            'precio.max'                => 'El precio no puede exceder $99.999.999,99.',
            'stockMinimo.integer'       => 'El stock mínimo debe ser un número entero (sin letras ni decimales).',
            'stockMinimo.min'           => 'El stock mínimo no puede ser negativo.',
            'stockMinimo.max'           => 'El stock mínimo no puede superar 9.999.999.',
            'categoria.required'        => 'La categoría es obligatoria.',
            'categoria.min'             => 'La categoría debe tener al menos 3 caracteres.',
            'categoria.regex'           => 'La categoría solo puede contener letras y espacios (no se permiten números).',
            'codigoBarras.regex'        => 'El código de barras solo debe contener números.',
            'codigoBarras.max'          => 'El código de barras no puede exceder 50 dígitos.',
            'diasPerecederoMax.integer' => 'Los días perecederos deben ser un número entero (sin letras ni decimales).',
            'diasPerecederoMax.min'     => 'Los días perecederos no pueden ser negativos.',
            'diasPerecederoMax.max'     => 'Los días perecederos máximos no pueden superar 3.650 días (10 años).',
            'unidad.required'           => 'La unidad de medida es obligatoria.',
            'unidad.in'                 => 'La unidad de medida seleccionada no es válida.',
            'proveedor_id.required'     => 'Debes seleccionar un proveedor.',
            'proveedor_id.exists'       => 'El proveedor seleccionado no es válido.',
            'imagen.image'              => 'El archivo debe ser una imagen válida (JPG, PNG, WEBP).',
            'imagen.mimes'              => 'La imagen debe ser de tipo JPG, PNG o WEBP.',
            'imagen.max'                => 'La imagen no debe superar los 2MB.',
        ];

        $data = $request->validate([
            'nombre'            => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\-\.\'\(\)]+$/u'],
            'descripcion'       => ['nullable', 'string', 'max:1000'],
            'precio'            => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'unidad'            => ['required', 'string', 'max:50', 'in:kg,g,lb,litro,saco,caja,unidad,bulto,canasta'],
            'stockMinimo'       => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'categoria'         => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'codigoBarras'      => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'diasPerecederoMax' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'imagen'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'proveedor_id'      => ['required', 'exists:proveedor,id'],
        ], $messages);

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

        $messages = [
            'nombre.required'           => 'El nombre del producto es obligatorio.',
            'nombre.min'                => 'El nombre debe tener al menos 3 caracteres.',
            'nombre.regex'              => 'El nombre solo puede contener letras y espacios (no se permiten números).',
            'descripcion.max'           => 'La descripción no puede superar los 1000 caracteres.',
            'precio.required'           => 'El precio es obligatorio.',
            'precio.numeric'            => 'El precio debe ser un número válido.',
            'precio.min'                => 'El precio no puede ser negativo.',
            'precio.max'                => 'El precio no puede exceder $99.999.999,99.',
            'stockMinimo.integer'       => 'El stock mínimo debe ser un número entero (sin letras ni decimales).',
            'stockMinimo.min'           => 'El stock mínimo no puede ser negativo.',
            'stockMinimo.max'           => 'El stock mínimo no puede superar 9.999.999.',
            'categoria.required'        => 'La categoría es obligatoria.',
            'categoria.min'             => 'La categoría debe tener al menos 3 caracteres.',
            'categoria.regex'           => 'La categoría solo puede contener letras y espacios (no se permiten números).',
            'codigoBarras.regex'        => 'El código de barras solo debe contener números.',
            'codigoBarras.max'          => 'El código de barras no puede exceder 50 dígitos.',
            'diasPerecederoMax.integer' => 'Los días perecederos deben ser un número entero (sin letras ni decimales).',
            'diasPerecederoMax.min'     => 'Los días perecederos no pueden ser negativos.',
            'diasPerecederoMax.max'     => 'Los días perecederos máximos no pueden superar 3.650 días (10 años).',
            'unidad.required'           => 'La unidad de medida es obligatoria.',
            'unidad.in'                 => 'La unidad de medida seleccionada no es válida.',
            'proveedor_id.required'     => 'Debes seleccionar un proveedor.',
            'proveedor_id.exists'       => 'El proveedor seleccionado no es válido.',
            'imagen.image'              => 'El archivo debe ser una imagen válida (JPG, PNG, WEBP).',
            'imagen.mimes'              => 'La imagen debe ser de tipo JPG, PNG o WEBP.',
            'imagen.max'                => 'La imagen no debe superar los 2MB.',
        ];

        $data = $request->validate([
            'nombre'            => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\-\.\'\(\)]+$/u'],
            'descripcion'       => ['nullable', 'string', 'max:1000'],
            'precio'            => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'unidad'            => ['required', 'string', 'max:50', 'in:kg,g,lb,litro,saco,caja,unidad,bulto,canasta'],
            'stockMinimo'       => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'categoria'         => ['required', 'string', 'min:3', 'max:100', 'regex:/^[\pL\s\-]+$/u'],
            'codigoBarras'      => ['nullable', 'string', 'max:50', 'regex:/^[0-9]+$/'],
            'diasPerecederoMax' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'imagen'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'proveedor_id'      => ['required', 'exists:proveedor,id'],
        ], $messages);

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
