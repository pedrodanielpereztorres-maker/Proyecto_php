<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioPdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/horarios/{seccion_id}/pdf', [HorarioPdfController::class, 'descargar'])
    ->name('horarios.pdf')
    ->middleware(['web', 'auth']);
