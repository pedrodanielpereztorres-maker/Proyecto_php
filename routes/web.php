<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HorarioPdfController;

use App\Livewire\PortalPublico;

Route::get('/', PortalPublico::class);

Route::get('/horarios/{seccion_id}/pdf', [HorarioPdfController::class, 'descargar'])
    ->name('horarios.pdf')
    ->middleware(['web', 'auth']);
