<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'profesor_id',
        'aula_id',
        'semestre_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }
}
