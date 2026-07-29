<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'capacidad', 'tipo_espacio_id'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function tipoEspacio()
    {
        return $this->belongsTo(TipoEspacio::class);
    }
}