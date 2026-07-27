<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turnos';

    // OBLIGATORIO: Permite guardar el nombre desde el formulario
    protected $fillable = [
        'nombre',
    ];
}
