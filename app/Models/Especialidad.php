<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'carrera_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }
}
