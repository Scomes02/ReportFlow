<?php

use App\Http\Controllers\Medico\MedicoController;
use Illuminate\Support\Facades\Route;

// Rutas para el módulo de Médico
Route::get('/estudios', [MedicoController::class, 'index'])->name('estudios.index');
Route::post('/estudios/{id}/informar', [MedicoController::class, 'guardarInforme'])->name('estudios.informar');
