<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'es_receso',
        'materia_id',
        'profesor_id',
        'espacio_id',
        'seccion_id',
        'periodo_academico_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'omitir_validacion_capacidad',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'es_receso' => 'boolean',
        'omitir_validacion_capacidad' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(static function (Horario $horario): void {
            static::validarHorario($horario);
        });

        static::updating(static function (Horario $horario): void {
            static::validarHorario($horario);
        });
    }

    protected static function validarHorario(Horario $horario): void
    {
        if (!$horario->hora_inicio || !$horario->hora_fin) {
            return;
        }

        if ($horario->es_receso) {
            return; // No se aplican validaciones de choques de profe/espacio a los recesos
        }

        if ($horario->hora_inicio->gte($horario->hora_fin)) {
            throw ValidationException::withMessages([
                'hora_fin' => 'La hora de fin debe ser posterior a la hora de inicio.',
            ]);
        }

        static::validarSinChoques($horario);
        static::validarRestriccionesProfesor($horario);
        static::validarCapacidadEspacio($horario);
        static::validarFatigaEstudiantil($horario);
        static::validarHorasSemanales($horario);
    }

    protected static function validarSinChoques(Horario $horario): void
    {
        $periodoId = $horario->periodo_academico_id;

        $conflictoProfesor = static::where('periodo_academico_id', $periodoId)
            ->where('dia_semana', $horario->dia_semana)
            ->where('profesor_id', $horario->profesor_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoProfesor) {
            throw ValidationException::withMessages([
                'profesor_id' => '¡Choque detectado! El profesor ya está dando otra clase a esa misma hora.',
            ]);
        }

        $conflictoEspacio = static::where('periodo_academico_id', $periodoId)
            ->where('dia_semana', $horario->dia_semana)
            ->where('espacio_id', $horario->espacio_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($conflictoEspacio) {
            throw ValidationException::withMessages([
                'espacio_id' => 'Este espacio físico ya se encuentra ocupado a esa hora por otra sección.',
            ]);
        }
    }

    protected static function validarRestriccionesProfesor(Horario $horario): void
    {
        $existeRestriccion = ProfesorRestriccion::query()
            ->where('profesor_id', $horario->profesor_id)
            ->where('dia_semana', $horario->dia_semana)
            ->where('hora_inicio', '<', $horario->hora_fin)
            ->where('hora_fin', '>', $horario->hora_inicio)
            ->exists();

        if ($existeRestriccion) {
            throw ValidationException::withMessages([
                'profesor_id' => 'El docente indicó en sus restricciones que no tiene disponibilidad para dar clases en este horario.',
            ]);
        }
    }

    protected static function validarCapacidadEspacio(Horario $horario): void
    {
        if ($horario->omitir_validacion_capacidad) {
            return;
        }

        if (!$horario->espacio_id || !$horario->seccion_id) {
            return;
        }

        $espacio = Espacio::find($horario->espacio_id);
        $seccion = Seccion::find($horario->seccion_id);

        if (!$espacio || !$seccion) {
            return;
        }

        if ($seccion->cantidad_alumnos > $espacio->capacidad_maxima) {
            throw ValidationException::withMessages([
                'espacio_id' => 'El espacio seleccionado es muy pequeño para la cantidad de alumnos de esta sección.',
            ]);
        }
    }

    protected static function validarFatigaEstudiantil(Horario $horario): void
    {
        if (!$horario->materia_id || !$horario->seccion_id || !$horario->hora_inicio || !$horario->hora_fin) {
            return;
        }

        $duracionNuevaMinutos = $horario->hora_inicio->diffInMinutes($horario->hora_fin);

        $horariosExistentes = static::where('periodo_academico_id', $horario->periodo_academico_id)
            ->where('dia_semana', $horario->dia_semana)
            ->where('seccion_id', $horario->seccion_id)
            ->where('materia_id', $horario->materia_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->get();

        $duracionExistenteMinutos = 0;
        foreach ($horariosExistentes as $h) {
            if ($h->hora_inicio && $h->hora_fin) {
                $duracionExistenteMinutos += $h->hora_inicio->diffInMinutes($h->hora_fin);
            }
        }

        // Límite de 3 bloques de 40 min = 120 min diarios por materia
        if (($duracionExistenteMinutos + $duracionNuevaMinutos) > 120) {
            throw ValidationException::withMessages([
                'materia_id' => 'Para evitar agotar a los alumnos, no deberías asignar más de 3 bloques continuos de esta materia el mismo día.',
            ]);
        }
    }

    protected static function validarHorasSemanales(Horario $horario): void
    {
        if (!$horario->materia_id || !$horario->seccion_id || !$horario->hora_inicio || !$horario->hora_fin) {
            return;
        }

        $materia = Materia::find($horario->materia_id);
        if (!$materia || !$materia->horas_semanales) {
            return;
        }

        $duracionNuevaMinutos = $horario->hora_inicio->diffInMinutes($horario->hora_fin);

        $horariosExistentes = static::where('periodo_academico_id', $horario->periodo_academico_id)
            ->where('seccion_id', $horario->seccion_id)
            ->where('materia_id', $horario->materia_id)
            ->where('id', '!=', $horario->id ?? 0)
            ->get();

        $minutosExistentes = 0;
        foreach ($horariosExistentes as $h) {
            if ($h->hora_inicio && $h->hora_fin && !$h->es_receso) {
                $minutosExistentes += $h->hora_inicio->diffInMinutes($h->hora_fin);
            }
        }

        // 1 hora académica = 40 minutos en este sistema
        $minutosMaximos = $materia->horas_semanales * 40;

        if (($minutosExistentes + $duracionNuevaMinutos) > $minutosMaximos) {
            throw ValidationException::withMessages([
                'materia_id' => "Ya has alcanzado el límite de horas permitidas a la semana para la materia '{$materia->nombre}' según su pénsum.",
            ]);
        }
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }

    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function semestre(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }
}
