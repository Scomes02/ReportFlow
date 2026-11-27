<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CodigoDobleFactor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login primario (email + contraseña)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if (Auth::validate($credentials)) {
            $user = User::where('email', $credentials['email'])->first();

            // Si el usuario tiene 2FA habilitado (por defecto activo)
            if ($user->two_factor_enabled) {
                $codigo = $user->generarCodigo2FA();

                $request->session()->put([
                    'auth.2fa.user_id' => $user->id,
                    'auth.2fa.remember' => $request->boolean('remember'),
                ]);

                try {
                    Mail::to($user->email)->send(new CodigoDobleFactor($codigo, $user));
                } catch (Throwable $e) {
                    report($e);
                }

                return redirect()->route('2fa.challenge');
            }

            // Si 2FA está deshabilitado para este usuario, ingresa directo
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Muestra la pantalla de verificación 2FA
     */
    public function show2FA(Request $request)
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget('auth.2fa.user_id');
            return redirect()->route('login');
        }

        $codigoDemo = $user->two_factor_code;
        $emailOculto = preg_replace('/(?<=.{2}).(?=.*@)/u', '*', $user->email);

        return view('auth.two-factor', compact('user', 'codigoDemo', 'emailOculto'));
    }

    /**
     * Procesa y valida el código 2FA ingresado
     */
    public function verify2FA(Request $request)
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'codigo' => 'required|string',
        ], [
            'codigo.required' => 'Ingresá el código de verificación de 6 dígitos.',
        ]);

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget('auth.2fa.user_id');
            return redirect()->route('login');
        }

        $codigoIngresado = trim(str_replace(' ', '', (string) $request->input('codigo')));

        if (! $user->validarCodigo2FA($codigoIngresado)) {
            return back()->withErrors([
                'codigo' => 'El código de verificación es incorrecto o ha expirado.',
            ]);
        }

        // Código válido: limpiar código y formalizar sesión
        $user->limpiarCodigo2FA();
        $remember = (bool) $request->session()->get('auth.2fa.remember', false);
        $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Reenvía un nuevo código 2FA
     */
    public function resend2FA(Request $request)
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $codigo = $user->generarCodigo2FA();

        try {
            Mail::to($user->email)->send(new CodigoDobleFactor($codigo, $user));
        } catch (Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Te hemos enviado un nuevo código de verificación.');
    }

    /**
     * Cancela la verificación 2FA y regresa al login
     */
    public function cancel2FA(Request $request)
    {
        $request->session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);

        return redirect()->route('login');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Redirigir según el rol del usuario
     */
    private function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'tecnico':
                return redirect()->intended('/tecnico/estudios');
            case 'medico':
                return redirect()->intended('/medico/estudios');
            case 'rrhh':
                return redirect()->intended('/rrhh/dashboard');
            case 'callcenter':
                return redirect()->intended('/callcenter/informes');
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            default:
                return redirect()->intended('/');
        }
    }
}