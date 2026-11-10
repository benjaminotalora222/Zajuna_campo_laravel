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

        $messages = [
            'name.required'     => 'El nombre completo es obligatorio.',
            'name.min'          => 'El nombre debe tener al menos 3 caracteres.',
            'name.regex'        => 'El nombre completo solo puede contener letras y espacios (no se permiten números).',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'La confirmación de la contraseña no coincide.',
            'role_id.required'  => 'Debes seleccionar un rol.',
            'role_id.exists'    => 'El rol seleccionado no es válido.',
        ];

        $data = $request->validate([
            'name'          => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\-\.\'\(\)]+$/u'],
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'role_id'       => 'required|exists:roles,id',
            'activo'        => 'boolean',
            'acceso_ventas' => 'nullable|boolean',
        ], $messages);

        $data['password']      = Hash::make($data['password']);
        $data['activo']        = $request->boolean('activo', true);
        $data['acceso_ventas'] = $request->boolean('acceso_ventas', true);

        $usuario = User::create($data);

        // Si el rol es proveedor (3), crear registro en tabla proveedor y vincular
        if ((int) $data['role_id'] === 3) {
            $proveedor = Proveedor::create([
                'nombre'   => $usuario->name,
                'codigo'   => Proveedor::generarCodigo(),
                'correo'   => $usuario->email,
                'tipo'     => 'Local',
                'telefono' => null,
                'estado'   => $data['activo'],
            ]);
            $usuario->update(['proveedor_id' => $proveedor->id]);
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

        $messages = [
            'name.required'     => 'El nombre completo es obligatorio.',
            'name.min'          => 'El nombre debe tener al menos 3 caracteres.',
            'name.regex'        => 'El nombre completo solo puede contener letras y espacios (no se permiten números).',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'=> 'La confirmación de la contraseña no coincide.',
            'role_id.required'  => 'Debes seleccionar un rol.',
            'role_id.exists'    => 'El rol seleccionado no es válido.',
        ];

        $data = $request->validate([
            'name'          => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\-\.\'\(\)]+$/u'],
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'      => 'nullable|string|min:8|confirmed',
            'role_id'       => 'required|exists:roles,id',
            'activo'        => 'boolean',
            'acceso_ventas' => 'nullable|boolean',
        ], $messages);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['activo'] = $request->boolean('activo');
        if ($request->has('acceso_ventas')) {
            $data['acceso_ventas'] = $request->boolean('acceso_ventas');
        }

        $rolAnterior = $usuario->role_id;
        $usuario->update($data);

        // Sincronizar tabla proveedor según cambio de rol
        if ((int) $data['role_id'] === 3 && (int) $rolAnterior !== 3) {
            // Asignaron rol proveedor → crear registro si no existe
            $proveedor = Proveedor::firstOrCreate(
                ['correo' => $usuario->email],
                [
                    'nombre'   => $usuario->name,
                    'codigo'   => Proveedor::generarCodigo(),
                    'tipo'     => 'Local',
                    'telefono' => null,
                    'estado'   => $usuario->activo,
                ]
            );
            $usuario->update(['proveedor_id' => $proveedor->id]);
        } elseif ((int) $data['role_id'] !== 3 && (int) $rolAnterior === 3) {
            // Quitaron rol proveedor → eliminar registro y desvincular
            if ($usuario->proveedor_id) {
                Proveedor::where('id', $usuario->proveedor_id)->delete();
            } else {
                Proveedor::where('correo', $usuario->email)->delete();
            }
            $usuario->update(['proveedor_id' => null]);
        } elseif ((int) $data['role_id'] === 3) {
            // Sigue siendo proveedor → actualizar nombre/estado y asegurar proveedor_id
            $proveedor = null;
            if ($usuario->proveedor_id) {
                $proveedor = Proveedor::find($usuario->proveedor_id);
            }
            if (!$proveedor) {
                $proveedor = Proveedor::where('correo', $usuario->email)->first();
            }

            if ($proveedor) {
                $proveedor->update([
                    'nombre' => $usuario->name,
                    'estado' => $usuario->activo,
                    'correo' => $usuario->email,
                ]);
                if (!$usuario->proveedor_id) {
                    $usuario->update(['proveedor_id' => $proveedor->id]);
                }
            } else {
                $proveedor = Proveedor::create([
                    'nombre'   => $usuario->name,
                    'codigo'   => Proveedor::generarCodigo(),
                    'correo'   => $usuario->email,
                    'tipo'     => 'Local',
                    'telefono' => null,
                    'estado'   => $usuario->activo,
                ]);
                $usuario->update(['proveedor_id' => $proveedor->id]);
            }
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

    /** Alternar acceso al módulo de ventas para un usuario (especialmente rol operativo) */
    public function toggleVentas(User $usuario)
    {
        $this->soloSuperadmin();

        $usuario->update(['acceso_ventas' => !$usuario->acceso_ventas]);

        $estado = $usuario->acceso_ventas ? 'habilitado' : 'bloqueado';
        LogAuditoria::registrar('Permisos', 'Actualización', "Se {$estado} el acceso al módulo de ventas para {$usuario->name} ({$usuario->email}).");

        if (request()->expectsJson()) {
            return response()->json([
                'ok' => true,
                'acceso_ventas' => $usuario->acceso_ventas,
                'message' => "Acceso a ventas {$estado} para {$usuario->name}."
            ]);
        }

        return back()->with('success', "Acceso a ventas {$estado} para el usuario {$usuario->name}.");
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
