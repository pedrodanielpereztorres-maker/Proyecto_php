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
        'semestre_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(fn ($horario) => static::validarSinChoques($horario));
        static::updating(fn ($horario) => static::validarSinChoques($horario));
    }

    /**
     * Valida que no exista un choque de horario para el profesor ni para el aula
     * dentro del mismo semestre, día y rango de horas.
     */
    protected static function validarSinChoques(Horario $horario): void
    {
        $conflictoProfesor = static::where('semestre_id', $horario->semestre_id)
            ->where('dia_semana', $horario->dia_semana)
            ->where('profesor_id', $horario->profesor_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoProfesor) {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor ya tiene una clase asignada en ese día y rango de horas para este semestre.',
            ]);
        }

        $conflictoAula = static::where('semestre_id', $horario->semestre_id)
            ->where('dia_semana', $horario->dia_semana)
            ->where('aula_id', $horario->aula_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoAula) {
            throw ValidationException::withMessages([
                'aula_id' => 'El aula ya está ocupada en ese día y rango de horas para este semestre.',
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

    public function semestre()
    {
        return $this->belongsTo(Semestre::class);
    }
}
