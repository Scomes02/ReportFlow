<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Procesar el login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirigir según el rol
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
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
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            default:
                return redirect()->intended('/');
        }
    }
}
