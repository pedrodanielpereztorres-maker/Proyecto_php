<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Espacio extends Model
{
    use HasFactory;

    protected $table = 'espacios';

    protected $fillable = [
        'codigo',
        'nombre',
        'capacidad_maxima',
        'tipo_espacio_id',
        'estatus_operativo',
    ];

    protected $casts = [
        'estatus_operativo' => 'string',
    ];

    // ─── Relaciones ───────────────────────────────────────────────────────────

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function tipoEspacio()
    {
        return $this->belongsTo(TipoEspacio::class);
    }
}
