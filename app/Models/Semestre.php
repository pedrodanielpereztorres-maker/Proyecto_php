<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semestre extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'activo'];

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
