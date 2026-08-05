<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class ProfesorRestriccion extends Model
{
    use HasFactory;

    protected $table = 'profesor_restriccions';

    protected $fillable = [
        'profesor_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'motivo',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    protected static function booted(): void
    {
        static::saving(static function (ProfesorRestriccion $restriccion): void {
            if ($restriccion->hora_inicio && $restriccion->hora_fin && $restriccion->hora_inicio->gte($restriccion->hora_fin)) {
                throw ValidationException::withMessages([
                    'hora_fin' => 'La hora de fin debe ser posterior a la hora de inicio.',
                ]);
            }
        });
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }
}
