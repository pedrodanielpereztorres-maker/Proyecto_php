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
        'color_principal',
        'color_secundario',
        'email_contacto',
        'telefono_contacto',
        'direccion',
        'pie_pagina_pdf',
        'director_academico',
        'coordinador_general'
    ];
}
