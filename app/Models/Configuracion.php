<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configuracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'siglas',
        'logo',
        'logo_url',
        'favicon',
        'color_principal'
    ];
}
