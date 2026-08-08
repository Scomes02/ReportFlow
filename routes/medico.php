<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Medico\MedicoController;

// 1. Worklist de Estudios (URL: /medico/estudios)
Route::get('/estudios', [MedicoController::class, 'index'])->name('estudios.index');

// 2. Redirección por si entran a /medico a secas
Route::get('/', function () {
    return redirect()->route('medico.estudios.index');
});

// 3. Mis Informes (URL: /medico/mis-informes)
Route::get('/mis-informes', [MedicoController::class, 'misInformes'])->name('mis-informes');

// 4. Procesar Informe (URL: /medico/estudios/{id}/informar)
Route::post('/estudios/{id}/informar', [MedicoController::class, 'guardarInforme'])->name('estudios.informar');

// 5. Rechazar Estudio, devolverlo al técnico con un motivo (URL: /medico/estudios/{id}/rechazar)
Route::post('/estudios/{id}/rechazar', [MedicoController::class, 'rechazarEstudio'])->name('estudios.rechazar');

// 6. Editar Informe (URL: /medico/informes/{id})
Route::put('/informes/{id}', [MedicoController::class, 'updateInforme'])->name('informes.update');

// 7. Añadir Adenda (URL: /medico/estudios/{id}/adendas)
Route::post('/estudios/{id}/adendas', [MedicoController::class, 'guardarAdenda'])->name('adendas.store');