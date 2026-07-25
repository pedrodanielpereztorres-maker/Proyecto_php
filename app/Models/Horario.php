<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'materia_id',
        'profesor_id',
        'aula_id',
        'periodo_academico_id',
        'semestre_id', // Para compatibilidad
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($horario) {
            if (!$horario->periodo_academico_id && $horario->semestre_id) {
                $horario->periodo_academico_id = $horario->semestre_id;
            }
            static::validarSinChoques($horario);
        });

        static::updating(function ($horario) {
            if (!$horario->periodo_academico_id && $horario->semestre_id) {
                $horario->periodo_academico_id = $horario->semestre_id;
            }
            static::validarSinChoques($horario);
        });
    }

    /**
     * Valida que no exista un choque de horario para el profesor ni para el aula
     * dentro del mismo período académico, día y rango de horas.
     */
    protected static function validarSinChoques(Horario $horario): void
    {
        $periodoId = $horario->periodo_academico_id ?? $horario->semestre_id;

        $conflictoProfesor = static::where(function ($q) use ($periodoId) {
                $q->where('periodo_academico_id', $periodoId)
                  ->orWhere('semestre_id', $periodoId);
            })
            ->where('dia_semana', $horario->dia_semana)
            ->where('profesor_id', $horario->profesor_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoProfesor) {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor ya tiene una clase asignada en ese día y rango de horas para este período académico.',
            ]);
        }

        $conflictoAula = static::where(function ($q) use ($periodoId) {
                $q->where('periodo_academico_id', $periodoId)
                  ->orWhere('semestre_id', $periodoId);
            })
            ->where('dia_semana', $horario->dia_semana)
            ->where('aula_id', $horario->aula_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoAula) {
            throw ValidationException::withMessages([
                'aula_id' => 'El aula ya está ocupada en ese día y rango de horas para este período académico.',
            ]);
        }
    }

    // ─── Relaciones ───────────────────────────────────────────────────────────

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

    public function periodoAcademico()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function semestre()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }
}
