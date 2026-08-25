<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioPdfController;

use App\Livewire\PortalPublico;

Route::get('/', PortalPublico::class);

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/horarios/{seccion_id}/pdf', [HorarioPdfController::class, 'descargar'])
    ->name('horarios.pdf')
    ->middleware(['web', 'auth']);

Route::get('/profesores/{profesor_id}/pdf', [HorarioPdfController::class, 'descargarProfesor'])
    ->name('profesores.pdf')
    ->middleware(['web', 'auth']);
