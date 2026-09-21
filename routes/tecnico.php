<?php

use App\Http\Controllers\Tecnico\EstudioController;
use Illuminate\Support\Facades\Route;

// Vista principal (Worklist del Técnico)
Route::get('/estudios', [EstudioController::class, 'index'])->name('estudios.index');

// Guardar un nuevo estudio
Route::post('/estudios', [EstudioController::class, 'store'])->name('estudios.store');

// Redirección por defecto si entran a /tecnico a secas
Route::get('/', function () {
    return redirect()->route('tecnico.estudios.index');
});