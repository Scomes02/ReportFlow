<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Rutas del Médico
Route::get('/medico', [StudyController::class, 'index'])->name('medico.index');
Route::put('/medico/studies/{id}/report', [StudyController::class, 'processReport'])->name('medico.processReport');