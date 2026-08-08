<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Verifica que el usuario logueado tenga uno de los roles permitidos
     * para el grupo de rutas que está pidiendo. Si no coincide, lo saca
     * de ahí y lo manda a SU propio módulo (no a un error genérico),
     * para que no quede confundido viendo un 403 en blanco.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort_unless($user, 401);

            return redirect()->to($this->rutaDeInicio($user->role))
                ->with('status', 'No tenés permiso para acceder a esa sección.');
        }

        return $next($request);
    }

    private function rutaDeInicio(?string $role): string
    {
        return match ($role) {
            'tecnico' => '/tecnico/estudios',
            'medico' => '/medico/estudios',
            'rrhh' => '/rrhh/dashboard',
            default => '/login',
        };
    }
}