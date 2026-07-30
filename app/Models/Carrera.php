<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrera extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'nivel_academico_id',
        'departamento_id',
    ];

    public function materias(): HasMany
    {
        return $this->hasMany(Materia::class);
    }

    public function nivelAcademico(): BelongsTo
    {
        return $this->belongsTo(NivelAcademico::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function especialidades(): HasMany
    {
        return $this->hasMany(Especialidad::class);
    }
}
