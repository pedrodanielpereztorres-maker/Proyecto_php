<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;

    protected $fillable = ['cedula', 'nombre', 'apellido', 'email'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
