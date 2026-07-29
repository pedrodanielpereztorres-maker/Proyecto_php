<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoJornada extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function jornadaParametros()
    {
        return $this->hasMany(JornadaParametro::class);
    }
}
