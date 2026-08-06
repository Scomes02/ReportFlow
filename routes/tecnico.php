<?php

use App\Http\Controllers\Tecnico\EstudioController;
use Illuminate\Support\Facades\Route;

Route::get('/estudios', [EstudioController::class, 'index'])->name('estudios.index');
Route::post('/estudios', [EstudioController::class, 'store'])->name('estudios.store');