<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\Proveedor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    /** Listado con búsqueda y filtro */
    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = User::with('rol')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleId = $request->input('role_id')) {
            $query->where('role_id', $roleId);
        }

        if ($request->input('estado') !== null && $request->input('estado') !== '') {
            $query->where('activo', (bool) $request->input('estado'));
        }

        $usuarios = $query->paginate(10)->withQueryString();
        $roles    = Role::all();

        return view('superadmin.usuarios.index', compact('usuarios', 'roles'));
    }

    /** Formulario crear */
    public function create()
    {
        $this->soloSuperadmin();
        $roles = Role::all();
        return view('superadmin.usuarios.form', compact('roles'));
    }

    /** Guardar nuevo usuario */
    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'activo'   => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['activo']   = $request->boolean('activo', true);

        $usuario = User::create($data);

        // Si el rol es proveedor (3), crear registro en tabla proveedor
        if ((int) $data['role_id'] === 3) {
            Proveedor::create([
                'nombre'   => $usuario->name,
                'correo'   => $usuario->email,
                'tipo'     => 'Proveedor',
                'telefono' => null,
                'estado'   => $data['activo'],
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Usuario creado correctamente.']);
        }

        LogAuditoria::registrar('Usuarios', 'Creación', "Se creó el usuario: {$usuario->name} ({$usuario->email})");

        return redirect()->route('superadmin.usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    /** Datos de un usuario para el modal de edición */
    public function show(User $usuario)
    {
        $this->soloSuperadmin();
        return response()->json($usuario->load('rol'));
    }

    /** Actualizar usuario */
    public function update(Request $request, User $usuario)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
            'activo'   => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo');

        $rolAnterior = $usuario->role_id;
        $usuario->update($data);

        // Sincronizar tabla proveedor según cambio de rol
        if ((int) $data['role_id'] === 3 && (int) $rolAnterior !== 3) {
            // Asignaron rol proveedor → crear registro si no existe
            Proveedor::firstOrCreate(
                ['correo' => $usuario->email],
                ['nombre' => $usuario->name, 'tipo' => 'Proveedor', 'telefono' => null, 'estado' => $usuario->activo]
            );
        } elseif ((int) $data['role_id'] !== 3 && (int) $rolAnterior === 3) {
            // Quitaron rol proveedor → eliminar registro
            Proveedor::where('correo', $usuario->email)->delete();
        } elseif ((int) $data['role_id'] === 3) {
            // Sigue siendo proveedor → actualizar nombre/estado
            Proveedor::where('correo', $usuario->email)
                ->update(['nombre' => $usuario->name, 'estado' => $usuario->activo]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Usuario actualizado correctamente.']);
        }

        LogAuditoria::registrar('Usuarios', 'Actualización', "Se actualizó el usuario: {$usuario->name} ({$usuario->email})");

        return redirect()->route('superadmin.usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    /** Activar / desactivar */
    public function toggleActivo(User $usuario)
    {
        $this->soloSuperadmin();
        $usuario->update(['activo' => !$usuario->activo]);

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'activo' => $usuario->activo]);
        }

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    /** Eliminar */
    public function destroy(User $usuario)
    {
        $this->soloSuperadmin();

        if ($usuario->id === auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'No puedes eliminar tu propia cuenta.'], 422);
            }
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Si era proveedor, eliminar su registro en tabla proveedor
        if ((int) $usuario->role_id === 3) {
            Proveedor::where('correo', $usuario->email)->delete();
        }

        $usuario->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Usuario eliminado correctamente.']);
        }

        return redirect()->route('superadmin.usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}
