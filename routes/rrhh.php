<?php

use App\Http\Controllers\Rrhh\RrhhController;
use Illuminate\Support\Facades\Route;

// Panel principal
Route::get('/dashboard', [RrhhController::class, 'index'])->name('dashboard');

// Carpetas por especialidad
Route::get('/especialidades', [RrhhController::class, 'especialidades'])->name('especialidades');

// Médicos que informaron dentro de una especialidad
Route::get('/medicos/{especialidad}', [RrhhController::class, 'medicos'])->name('medicos');

// Listado paginado de todos los informes de un médico (por nombre)
Route::get('/medico/{nombre}/informes', [RrhhController::class, 'informesMedico'])->name('informes.medico');

// Resumen de un médico: cuántos informes hizo, agrupados por mes (sin montos)
Route::get('/medicos/{medicoId}/resumen', [RrhhController::class, 'resumenInformesMedico'])->name('medicos.resumen');

// Archivo general de informes firmados, por mes
Route::get('/archivo', [RrhhController::class, 'archivoGeneral'])->name('archivo');
Route::get('/archivo/mes/{mes}', [RrhhController::class, 'archivoMes'])->name('archivo.mes');