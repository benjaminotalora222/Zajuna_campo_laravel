<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CodigoRecuperacionMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset code request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Por favor ingresa tu correo electrónico.',
            'email.email'    => 'Ingresa una dirección de correo válida.',
            'email.exists'   => 'No encontramos ninguna cuenta registrada con este correo.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generar código numérico seguro de 6 dígitos
        $codigo = (string) random_int(100000, 999999);

        // Guardar o actualizar en password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => Hash::make($codigo),
                'created_at' => now(),
            ]
        );

        // Enviar correo con el código
        try {
            Mail::to($user->email)->send(new CodigoRecuperacionMail($codigo, $user->name));
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de recuperación: ' . $e->getMessage());
        }

        session(['reset_email' => $request->email]);

        // Si el mailer es 'log', pasamos dev_code a la sesión para pruebas locales sin servidor SMTP
        if (config('mail.default') === 'log') {
            session(['dev_code' => $codigo]);
        }

        return redirect()->route('password.reset')
            ->with('status', 'Hemos enviado un código de 6 dígitos a tu correo: ' . $request->email);
    }
}
