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
        // 1. Tipos de Jornada y Parámetros por defecto
        $tipoSemana = \App\Models\TipoJornada::firstOrCreate(['nombre' => 'Semana']);
        JornadaParametro::firstOrCreate([
            'tipo_jornada_id' => $tipoSemana->id,
        ], [
            'duracion_bloque_minutos' => 45,
            'duracion_receso_minutos' => 15,
            'hora_inicio' => '07:30:00',
            'hora_fin' => '21:00:00',
        ]);

        $tipoSabatino = \App\Models\TipoJornada::firstOrCreate(['nombre' => 'Sabatino']);
        JornadaParametro::firstOrCreate([
            'tipo_jornada_id' => $tipoSabatino->id,
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
        // 3. Turnos por defecto
        \App\Models\Turno::firstOrCreate(['nombre' => 'Matutino']);
        \App\Models\Turno::firstOrCreate(['nombre' => 'Vespertino']);
        \App\Models\Turno::firstOrCreate(['nombre' => 'Nocturno']);

        // 4. Departamentos/Coordinaciones
        \App\Models\Departamento::updateOrCreate(['nombre' => 'Sistemas'], ['descripcion' => 'Coordinación de Sistemas']);
        \App\Models\Departamento::updateOrCreate(['nombre' => 'Electrónica'], ['descripcion' => 'Coordinación de Electrónica']);
        \App\Models\Departamento::updateOrCreate(['nombre' => 'Administración'], ['descripcion' => 'Coordinación de Administración']);
        // 5. Configuración del Sistema
        \App\Models\Configuracion::firstOrCreate(['id' => 1], [
            'nombre' => 'Instituto Universitario de Tecnología Para la Informática',
            'siglas' => 'IUTEPI',
            'color_principal' => '#16a34a',
        ]);
    }
}
