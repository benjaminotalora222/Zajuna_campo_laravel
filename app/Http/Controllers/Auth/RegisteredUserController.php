<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear el registro de proveedor
            $proveedor = Proveedor::create([
                'nombre'  => $request->name,
                'tipo'    => 'externo',
                'correo'  => $request->email,
                'estado'  => true,
            ]);

            // 2. Crear el usuario con role_id = 3 (proveedor) y proveedor_id vinculado
            $user = User::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'password'     => Hash::make($request->password),
                'role_id'      => 3,
                'proveedor_id' => $proveedor->id,
                'activo'       => true,
            ]);

            event(new Registered($user));

            Auth::login($user);
        });

        return redirect(route('proveedor.dashboard', absolute: false));
    }
}
