<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'capacidad', 'tipo'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
