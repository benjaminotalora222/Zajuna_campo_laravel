<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $email = $request->query('email', session('reset_email', ''));
        $devCode = session('dev_code');

        return view('auth.reset-password', [
            'email'    => $email,
            'dev_code' => $devCode,
        ]);
    }

    /**
     * Handle an incoming new password request with verification code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'                 => ['required', 'email', 'exists:users,email'],
            'codigo'                => ['required', 'string', 'size:6'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'email.required'        => 'El correo electrónico es obligatorio.',
            'email.email'           => 'Ingresa un correo electrónico válido.',
            'email.exists'          => 'No encontramos ninguna cuenta con este correo.',
            'codigo.required'       => 'Por favor ingresa el código de 6 dígitos enviado a tu correo.',
            'codigo.size'           => 'El código de recuperación debe contener exactamente 6 dígitos.',
            'password.required'     => 'Debes ingresar una nueva contraseña.',
            'password.confirmed'    => 'La confirmación de la contraseña no coincide.',
            'password.min'          => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withInput($request->only('email'))
                ->withErrors(['codigo' => 'No hay ninguna solicitud de recuperación activa para este correo. Por favor solicita un nuevo código.']);
        }

        // Expiración: 15 minutos
        if (Carbon::parse($resetRecord->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withInput($request->only('email'))
                ->withErrors(['codigo' => 'El código de recuperación ha expirado (validez de 15 minutos). Por favor solicita uno nuevo.']);
        }

        // Comprobar código
        if (!Hash::check($request->codigo, $resetRecord->token)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['codigo' => 'El código ingresado es incorrecto. Revisa tu correo e inténtalo de nuevo.']);
        }

        // Actualizar contraseña del usuario
        $user = User::where('email', $request->email)->first();
        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Eliminar token usado y limpiar sesión
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        session()->forget(['reset_email', 'dev_code']);

        event(new PasswordReset($user));

        return redirect()->route('login')
            ->with('status', '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
    }
}
