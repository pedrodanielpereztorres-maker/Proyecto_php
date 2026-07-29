<?php

namespace App\Models;

class Semestre extends PeriodoAcademico
{
    protected $table = 'periodos_academicos';

    protected $fillable = [
        'codigo',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'periodo_academico_id');
    }
}
