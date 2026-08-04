<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::
    // middleware(['auth'])->
    prefix('tecnico')
    ->name('tecnico.')
    ->group(base_path('routes/tecnico.php'));

if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        auth()->loginUsingId(\App\Models\User::where('email', 'tecnico.prueba@reportflow.local')->first()->id);
        return redirect('/tecnico/estudios');
    });
}