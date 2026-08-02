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
        'espacio_id',
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
     * Valida que no exista un choque de horario para el profesor ni para el espacio
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

        $conflictoEspacio = static::where(function ($q) use ($periodoId) {
                $q->where('periodo_academico_id', $periodoId)
                  ->orWhere('semestre_id', $periodoId);
            })
            ->where('dia_semana', $horario->dia_semana)
            ->where('espacio_id', $horario->espacio_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoEspacio) {
            throw ValidationException::withMessages([
                'espacio_id' => 'El espacio ya está ocupado en ese día y rango de horas para este período académico.',
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

    public function espacio()
    {
        return $this->belongsTo(Espacio::class);
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
