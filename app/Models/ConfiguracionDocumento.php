<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionDocumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'membrete_encabezado',
        'membrete_pie',
        'marca_de_agua',
    ];
}
