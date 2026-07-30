<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NivelAcademico extends Model
{
    use HasFactory;

    protected $table = 'niveles_academicos';

    protected $fillable = [
        'nombre',
        'siglas',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function carreras(): HasMany
    {
        return $this->hasMany(Carrera::class);
    }
}
