<?php

use App\Http\Controllers\Rrhh\RrhhController;
use Illuminate\Support\Facades\Route;

// Rutas para el módulo de RRHH
Route::get('/dashboard', [RrhhController::class, 'index'])->name('dashboard');
Route::get('/especialidades', [RrhhController::class, 'especialidades'])->name('especialidades');
Route::get('/medicos/{especialidad}', [RrhhController::class, 'medicos'])->name('medicos');
Route::get('/medico/{nombre}/informes', [RrhhController::class, 'informesMedico'])->name('informes.medico');
Route::get('/archivo', [RrhhController::class, 'archivoGeneral'])->name('archivo');
Route::get('/archivo/mes/{mes}', [RrhhController::class, 'archivoMes'])->name('archivo.mes');
Route::get('/liquidacion/{medicoId}', [RrhhController::class, 'detalleLiquidacion'])->name('liquidacion.detalle');