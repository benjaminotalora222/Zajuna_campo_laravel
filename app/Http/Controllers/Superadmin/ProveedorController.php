<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = Proveedor::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre',   'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhere('telefono','like', "%{$search}%");
            });
        }

        if ($tipo = $request->input('tipo')) {
            $query->where('tipo', $tipo);
        }

        if ($request->input('estado') !== null && $request->input('estado') !== '') {
            $query->where('estado', (bool) $request->input('estado'));
        }

        $proveedores = $query->paginate(10)->withQueryString();
        $tipos       = Proveedor::whereNotNull('tipo')->distinct()->pluck('tipo');

        return view('superadmin.proveedores.index', compact('proveedores', 'tipos'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'tipo'     => 'required|string|max:255',
            'correo'   => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'estado'   => 'boolean',
        ]);

        $data['estado'] = $request->boolean('estado', true);
        Proveedor::create($data);

        LogAuditoria::registrar('Proveedores', 'Creación', "Se creó el proveedor: {$data['nombre']}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor creado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'tipo'     => 'required|string|max:255',
            'correo'   => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'estado'   => 'boolean',
        ]);

        $data['estado'] = $request->boolean('estado');
        $proveedor->update($data);

        LogAuditoria::registrar('Proveedores', 'Actualización', "Se actualizó el proveedor: {$proveedor->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function toggleEstado(Proveedor $proveedor)
    {
        $this->soloSuperadmin();
        $proveedor->update(['estado' => !$proveedor->estado]);
        return back()->with('success', 'Estado del proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $this->soloSuperadmin();
        $proveedor->delete();
        LogAuditoria::registrar('Proveedores', 'Eliminación', "Se eliminó el proveedor: {$proveedor->nombre}");
        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor eliminado correctamente.');
    }
}
