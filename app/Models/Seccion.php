<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'periodo_academico_id',
        'turno_id',
        'carrera_id',
        'semestre',
        'codigo',
        'cantidad_alumnos',
        'estado_horario',
    ];

    protected $casts = [
        'semestre' => 'string',
        'cantidad_alumnos' => 'integer',
    ];

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function turno(): BelongsTo
    {
        return $this->belongsTo(Turno::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'seccion_id');
    }
}
