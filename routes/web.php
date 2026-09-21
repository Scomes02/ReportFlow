<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Ruta pública raíz
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas por autenticación + rol
|--------------------------------------------------------------------------
| Cada grupo exige estar logueado (middleware 'auth') Y tener el rol
| correspondiente (middleware 'role:xxx'). Si un usuario de otro rol
| entra a una de estas URLs -por link, por historial del navegador, o
| por escribirla a mano- el middleware 'role' lo redirige a SU propio
| módulo antes de que se renderice una sola línea de esa vista. Por eso
| nunca deberías "ver el módulo de RRHH" estando logueado como médico:
| lo que ves en ese caso es tu propio dashboard, con un aviso arriba.
*/
Route::middleware(['auth'])->group(function () {

    // TÉCNICO
    Route::prefix('tecnico')
        ->name('tecnico.')
        ->middleware('role:tecnico')
        ->group(base_path('routes/tecnico.php'));

    // MÉDICO
    Route::prefix('medico')
        ->name('medico.')
        ->middleware('role:medico')
        ->group(base_path('routes/medico.php'));

    // RRHH
    Route::prefix('rrhh')
        ->name('rrhh.')
        ->middleware('role:rrhh')
        ->group(base_path('routes/rrhh.php'));

    // CALL CENTER
    Route::prefix('callcenter')
        ->name('callcenter.')
        ->middleware('role:callcenter')
        ->group(base_path('routes/callcenter.php'));
});

/*
|--------------------------------------------------------------------------
| Rutas de desarrollo (SOLO entorno local)
|--------------------------------------------------------------------------
| Atajos para loguearse como cada rol sin pasar por el formulario, útiles
| mientras se prueba en el entorno local. No existen fuera de 'local'.
*/
if (app()->environment('local')) {

    Route::get('/dev-login', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'tecnico.prueba@reportflow.local'],
            [
                'name' => 'Técnico Prueba',
                'password' => bcrypt('password'),
                'role' => 'tecnico',
            ]
        );
        auth()->login($user);

        return redirect('/tecnico/estudios');
    });

    Route::get('/dev-login-medico', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'medico@reportflow.local'],
            [
                'name' => 'Dr. Juan Pérez',
                'password' => bcrypt('password'),
                'role' => 'medico',
            ]
        );
        auth()->login($user);

        return redirect('/medico/estudios');
    });

    Route::get('/dev-login-rrhh', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'rrhh@reportflow.local'],
            [
                'name' => 'RRHH - Administración',
                'password' => bcrypt('password'),
                'role' => 'rrhh',
            ]
        );
        auth()->login($user);

        return redirect('/rrhh/dashboard');
    });

    Route::get('/dev-login-callcenter', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'callcenter@reportflow.local'],
            [
                'name' => 'Call Center - Recepción',
                'password' => bcrypt('password'),
                'role' => 'callcenter',
            ]
        );
        auth()->login($user);

        return redirect('/callcenter/informes');
    });
}