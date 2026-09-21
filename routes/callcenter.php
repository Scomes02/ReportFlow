<?php

use App\Http\Controllers\CallCenter\CallCenterController;
use Illuminate\Support\Facades\Route;

// Listado de informes ya cerrados (firmados), con búsqueda opcional por DNI
Route::get('/informes', [CallCenterController::class, 'index'])->name('informes');

// Simula el envío del informe/aviso al paciente (no manda nada real)
Route::post('/informes/{id}/enviar', [CallCenterController::class, 'enviarAviso'])->name('informes.enviar');