<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JornadaParametro extends Model
{
    use HasFactory;

    protected $table = 'jornada_parametros';

    protected $fillable = [
        'tipo_jornada_id',
        'duracion_bloque_minutos',
        'duracion_receso_minutos',
        'hora_inicio',
        'hora_fin',
    ];

    public function tipoJornada()
    {
        return $this->belongsTo(TipoJornada::class);
    }
}
