<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Rrhh\RrhhController;


// ============================================
// AUTHENTICATION
// ============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// RUTAS PÚBLICAS
// ============================================
Route::get('/', function () {
    return redirect('/login');
});

// ============================================
// RUTAS PROTEGIDAS POR AUTENTICACIÓN
// ============================================
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
});

// ============================================
// RUTAS DE DESARROLLO (SOLO LOCAL)
// ============================================
if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'tecnico.prueba@reportflow.local'],
            [
                'name' => 'Técnico Prueba',
                'password' => bcrypt('password'),
                'role' => 'tecnico'
            ]
        );
        auth()->login($user);
        return redirect('/tecnico/estudios');
    });

    // Login Médico
    Route::get('/dev-login-medico', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'medico@reportflow.local'],
            [
                'name' => 'Dr. Juan Pérez',
                'password' => bcrypt('password'),
                'role' => 'medico'
            ]
        );
        Auth()->login($user);
        return redirect('/medico/estudios');
    });

    Route::get('/dev-login-rrhh', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'rrhh@reportflow.local'],
            [
                'name' => 'RRHH - Administración',
                'password' => bcrypt('password'),
                'role' => 'rrhh'
            ]
        );
        auth()->login($user);
        return redirect('/rrhh/dashboard');
    });
}
