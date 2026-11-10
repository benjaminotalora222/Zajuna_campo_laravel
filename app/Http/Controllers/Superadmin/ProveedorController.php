<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\OrdenPedido;
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

        $query = Proveedor::with('user')->latest();

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

        $messages = [
            'nombre.required'       => 'El nombre o razón social del proveedor es obligatorio.',
            'tipo.required'         => 'El tipo de proveedor es obligatorio.',
            'correo.required'       => 'El correo electrónico es obligatorio.',
            'correo.email'          => 'Ingresa un correo electrónico válido.',
            'correo.unique'         => 'Este correo electrónico ya está registrado por otro proveedor.',
            'codigo.unique'         => 'Este código de proveedor ya está en uso.',
            'telefono.regex'        => 'El teléfono solo debe contener números, espacios o guiones.',
            'password.required'     => 'La contraseña es obligatoria.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'La confirmación de la contraseña no coincide.',
        ];

        $request->validate([
            'nombre'                => 'required|string|max:255',
            'codigo'                => 'nullable|string|max:50|unique:proveedor,codigo',
            'tipo'                  => 'nullable|string|max:50',
            'correo'                => 'required|email|max:255|unique:proveedor,correo',
            'telefono'              => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\s\-\(\)]+$/'],
            'estado'                => 'boolean',
            'user_name'             => 'nullable|string|max:255',
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ], $messages);

        DB::transaction(function () use ($request) {
            $codigo = $request->filled('codigo')
                ? strtoupper(trim($request->codigo))
                : Proveedor::generarCodigo();

            $tipo = $request->filled('tipo') ? trim($request->tipo) : 'Local';
            $userName = $request->filled('user_name') ? trim($request->user_name) : trim($request->nombre);
            $estado = $request->boolean('estado', true);
            $correo = trim($request->correo);

            // 1. Crear el proveedor
            $proveedor = Proveedor::create([
                'nombre'   => trim($request->nombre),
                'codigo'   => $codigo,
                'tipo'     => $tipo,
                'correo'   => $correo,
                'telefono' => $request->filled('telefono') ? trim($request->telefono) : null,
                'estado'   => $estado,
            ]);

            // 2. Comprobar si ya existe un usuario con este correo
            $existingUser = User::where('email', $correo)->first();

            if ($existingUser) {
                if ((int) $existingUser->role_id === 3 && empty($existingUser->proveedor_id)) {
                    $existingUser->update([
                        'name'          => $userName,
                        'password'      => Hash::make($request->password),
                        'proveedor_id'  => $proveedor->id,
                        'activo'        => $estado,
                        'acceso_ventas' => true,
                    ]);
                } else {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'correo' => 'El correo electrónico ya está en uso por otro usuario en el sistema.',
                    ]);
                }
            } else {
                User::create([
                    'name'          => $userName,
                    'email'         => $correo,
                    'password'      => Hash::make($request->password),
                    'role_id'       => 3,
                    'proveedor_id'  => $proveedor->id,
                    'activo'        => $estado,
                    'acceso_ventas' => true,
                ]);
            }
        });

        LogAuditoria::registrar('Proveedores', 'Creación', "Se creó el proveedor con cuenta: {$request->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', "Proveedor '{$request->nombre}' y su cuenta de acceso registrados correctamente.");
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->soloSuperadmin();

        $messages = [
            'nombre.required'       => 'El nombre del proveedor es obligatorio.',
            'tipo.required'         => 'El tipo de proveedor es obligatorio.',
            'correo.required'       => 'El correo electrónico es obligatorio.',
            'correo.email'          => 'Ingresa un correo electrónico válido.',
            'correo.unique'         => 'Este correo electrónico ya está registrado por otro proveedor.',
            'codigo.unique'         => 'Este código de proveedor ya está en uso.',
            'telefono.regex'        => 'El teléfono solo debe contener números, espacios o guiones.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'    => 'La confirmación de la contraseña no coincide.',
        ];

        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'codigo'                => ['nullable', 'string', 'max:50', Rule::unique('proveedor', 'codigo')->ignore($proveedor->id)],
            'tipo'                  => 'nullable|string|max:50',
            'correo'                => ['required', 'email', 'max:255', Rule::unique('proveedor', 'correo')->ignore($proveedor->id)],
            'telefono'              => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\s\-\(\)]+$/'],
            'estado'                => 'boolean',
            'user_name'             => 'nullable|string|max:255',
            'password'              => 'nullable|string|min:8|confirmed',
        ], $messages);

        if (!empty($data['codigo'])) {
            $data['codigo'] = strtoupper(trim($data['codigo']));
        } elseif (empty($proveedor->codigo)) {
            $data['codigo'] = Proveedor::generarCodigo();
        }

        $data['estado'] = $request->boolean('estado');
        $data['tipo']   = $data['tipo'] ?? $proveedor->tipo ?? 'Local';

        $correoAnterior = $proveedor->correo;

        DB::transaction(function () use ($proveedor, $data, $request, $correoAnterior) {
            $proveedor->update([
                'nombre'   => trim($data['nombre']),
                'codigo'   => $data['codigo'],
                'tipo'     => $data['tipo'],
                'correo'   => trim($data['correo']),
                'telefono' => $request->filled('telefono') ? trim($data['telefono']) : null,
                'estado'   => $data['estado'],
            ]);

            $userName = $request->filled('user_name') ? trim($request->user_name) : trim($data['nombre']);

            // Sincronizar usuario vinculado
            $user = User::where('proveedor_id', $proveedor->id)
                ->orWhere('email', $correoAnterior)
                ->first();

            if ($user) {
                // Verificar que el nuevo email no esté ocupado por OTRO usuario
                $userConCorreo = User::where('email', trim($data['correo']))->where('id', '!=', $user->id)->first();
                if ($userConCorreo) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'correo' => 'El correo electrónico ya está en uso por otro usuario.',
                    ]);
                }

                $userUpdates = [
                    'name'         => $userName,
                    'email'        => trim($data['correo']),
                    'activo'       => $data['estado'],
                    'proveedor_id' => $proveedor->id,
                ];
                if ($request->filled('password')) {
                    $userUpdates['password'] = Hash::make($request->password);
                }
                $user->update($userUpdates);
            } else {
                // Si no existía usuario vinculado, verificar si existe por nuevo correo
                $userConCorreo = User::where('email', trim($data['correo']))->first();
                if ($userConCorreo) {
                    if ((int) $userConCorreo->role_id === 3 && empty($userConCorreo->proveedor_id)) {
                        $userConCorreo->update([
                            'name'         => $userName,
                            'proveedor_id' => $proveedor->id,
                            'activo'       => $data['estado'],
                        ]);
                    } else {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'correo' => 'El correo electrónico ya está en uso por otro usuario.',
                        ]);
                    }
                } else {
                    User::create([
                        'name'          => $userName,
                        'email'         => trim($data['correo']),
                        'password'      => Hash::make($request->filled('password') ? $request->password : '12345678'),
                        'role_id'       => 3,
                        'proveedor_id'  => $proveedor->id,
                        'activo'        => $data['estado'],
                        'acceso_ventas' => true,
                    ]);
                }
            }
        });

        LogAuditoria::registrar('Proveedores', 'Actualización', "Se actualizó el proveedor: {$proveedor->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor y cuenta de acceso actualizados correctamente.');
    }

    public function toggleEstado(Proveedor $proveedor)
    {
        $this->soloSuperadmin();
        $nuevoEstado = !$proveedor->estado;
        $proveedor->update(['estado' => $nuevoEstado]);

        User::where('proveedor_id', $proveedor->id)
            ->orWhere('email', $proveedor->correo)
            ->update(['activo' => $nuevoEstado]);

        $accion = $nuevoEstado ? 'activó' : 'desactivó';
        LogAuditoria::registrar('Proveedores', 'Estado', "Se {$accion} el proveedor: {$proveedor->nombre}");

        return back()->with('success', "Proveedor '{$proveedor->nombre}' " . ($nuevoEstado ? 'activado' : 'desactivado') . " correctamente.");
    }

    public function destroy(Proveedor $proveedor)
    {
        $this->soloSuperadmin();

        $tieneProductos = $proveedor->productos()->exists();
        $tieneOrdenes   = OrdenPedido::where('proveedor_id', $proveedor->id)->exists();

        if ($tieneProductos || $tieneOrdenes) {
            return redirect()->route('superadmin.proveedores.index')
                             ->with('error', "No se puede eliminar el proveedor '{$proveedor->nombre}' porque tiene productos u órdenes asociadas. Puedes desactivarlo para suspender su operación.");
        }

        DB::transaction(function () use ($proveedor) {
            User::where('proveedor_id', $proveedor->id)->delete();
            $proveedor->delete();
        });

        LogAuditoria::registrar('Proveedores', 'Eliminación', "Se eliminó el proveedor: {$proveedor->nombre}");

        return redirect()->route('superadmin.proveedores.index')
                         ->with('success', 'Proveedor eliminado correctamente.');
    }
}
