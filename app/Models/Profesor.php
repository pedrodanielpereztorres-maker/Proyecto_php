<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesor extends Model
{
    use HasFactory;

    protected $fillable = [
        'cedula',
        'nombre',
        'apellido',
        'email',
        'codigo_interno',
        'direccion',
        'telefono',
        'nivel_academico_id',
        'especialidad_id',
        'avatar',
    ];

    protected $casts = [
        'codigo_interno' => 'string',
        'direccion' => 'string',
        'telefono' => 'string',
    ];

    public function nivelAcademico(): BelongsTo
    {
        return $this->belongsTo(NivelAcademico::class, 'nivel_academico_id');
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function restricciones(): HasMany
    {
        return $this->hasMany(ProfesorRestriccion::class);
    }
}
