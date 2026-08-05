<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicoController;

Route::get('/', function () {
    return view('welcome');
});


// Bloque de rutas para modulo-medico

Route::prefix('medico')->name('medico.')->group(function () {
    Route::get('/estudios', [MedicoController::class, 'index'])->name('estudios.index');
    Route::post('/estudios/{id}/informar', [MedicoController::class, 'guardarInforme'])->name('estudios.informar');
});