<?php

namespace Database\Seeders;

use App\Models\JornadaParametro;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Parámetros de Jornada por defecto
        JornadaParametro::firstOrCreate([
            'tipo_jornada' => 'Semana',
        ], [
            'duracion_bloque_minutos' => 45,
            'duracion_receso_minutos' => 15,
            'hora_inicio' => '07:30:00',
            'hora_fin' => '21:00:00',
        ]);

        JornadaParametro::firstOrCreate([
            'tipo_jornada' => 'Sabatino',
        ], [
            'duracion_bloque_minutos' => 45,
            'duracion_receso_minutos' => 15,
            'hora_inicio' => '07:30:00',
            'hora_fin' => '17:00:00',
        ]);

        // 2. Período Académico por defecto
        PeriodoAcademico::firstOrCreate([
            'codigo' => 'PR26-2',
        ], [
            'fecha_inicio' => '2026-09-01',
            'fecha_fin' => '2026-12-15',
            'activo' => true,
        ]);
    }
}
