<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

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
                  ->orWhere('codigo', 'like', "%{$search}%")
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

        $request->validate([
            'nombre'                => 'required|string|max:255',
            'codigo'                => 'nullable|string|max:50|unique:proveedor,codigo',
            'correo'                => 'required|email|max:255|unique:users,email',
            'telefono'              => 'nullable|string|max:50',
            'estado'                => 'boolean',
            'user_name'             => 'required|string|max:255',
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            $codigo = $request->filled('codigo')
                ? strtoupper(trim($request->codigo))
                : Proveedor::generarCodigo();

            // 1. Crear el proveedor
            $proveedor = Proveedor::create([
                'nombre'   => $request->nombre,
                'codigo'   => $codigo,
                'tipo'     => 'externo',
                'correo'   => $request->correo,
                'telefono' => $request->telefono,
                'estado'   => $request->boolean('estado', true),
            ]);

            // 2. Crear el usuario vinculado
            User::create([
                'name'         => $request->user_name,
                'email'        => $request->correo,
                'password'     => Hash::make($request->password),
                'role_id'      => 3,
                'proveedor_id' => $proveedor->id,
                'activo'       => true,
            ]);
        });

        LogAuditoria::registrar('Proveedores', 'Creación', "Se creó el proveedor con cuenta: {$request->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', "Proveedor '{$request->nombre}' y su cuenta de acceso creados correctamente.");
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'   => 'required|string|max:255',
            'codigo'   => ['nullable', 'string', 'max:50', Rule::unique('proveedor', 'codigo')->ignore($proveedor->id)],
            'correo'   => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'estado'   => 'boolean',
        ]);

        if (!empty($data['codigo'])) {
            $data['codigo'] = strtoupper(trim($data['codigo']));
        } elseif (empty($proveedor->codigo)) {
            $data['codigo'] = Proveedor::generarCodigo();
        }

        $data['estado'] = $request->boolean('estado');
        $proveedor->update($data);

        User::where('proveedor_id', $proveedor->id)
            ->orWhere('email', $proveedor->correo)
            ->update(['activo' => $data['estado']]);

        LogAuditoria::registrar('Proveedores', 'Actualización', "Se actualizó el proveedor: {$proveedor->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function toggleEstado(Proveedor $proveedor)
    {
        $this->soloSuperadmin();
        $nuevoEstado = !$proveedor->estado;
        $proveedor->update(['estado' => $nuevoEstado]);

        User::where('proveedor_id', $proveedor->id)
            ->orWhere('email', $proveedor->correo)
            ->update(['activo' => $nuevoEstado]);

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
