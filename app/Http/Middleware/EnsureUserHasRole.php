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
     * de ahí y lo manda a SU propio módulo -no a un error genérico-, con
     * un mensaje que aclara explícitamente qué pasó: esto es lo que evita
     * que alguien piense que "el sidebar cambia solo" cuando en realidad
     * está viendo el módulo de su propio rol, no el que esperaba.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_unless($user, 401);

        if (! in_array($user->role, $roles, true)) {
            return redirect()->to($this->rutaDeInicio($user->role))
                ->with('status', "Tu usuario tiene el rol \"{$this->nombreRol($user->role)}\" y no puede acceder a esa sección. Te redirigimos a tu panel.");
        }

        return $next($request);
    }

    private function rutaDeInicio(?string $role): string
    {
        return match ($role) {
            'tecnico' => '/tecnico/estudios',
            'medico' => '/medico/estudios',
            'rrhh' => '/rrhh/dashboard',
            'callcenter' => '/callcenter/informes',
            default => '/login',
        };
    }

    private function nombreRol(?string $role): string
    {
        return match ($role) {
            'tecnico' => 'Técnico',
            'medico' => 'Médico',
            'rrhh' => 'RRHH / Administración',
            'callcenter' => 'Call Center',
            default => 'Desconocido',
        };
    }
}