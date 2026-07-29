<?php

namespace Database\Seeders;

use App\Models\TipoEspacio;
use Illuminate\Database\Seeder;

class TipoEspacioSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = ['Teoría', 'Laboratorio', 'Cancha', 'Auditorio'];

        foreach ($tipos as $tipo) {
            TipoEspacio::firstOrCreate(['nombre' => $tipo]);
        }
    }
}