<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\NivelAcademico;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NivelesAcademicosAndEspecialidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sembrar Niveles Académicos
        $niveles = [
            ['nombre' => 'Técnico Superior Universitario', 'siglas' => 'TSU', 'activo' => true],
            ['nombre' => 'Ingeniero', 'siglas' => 'Ing.', 'activo' => true],
            ['nombre' => 'Licenciado', 'siglas' => 'Lic.', 'activo' => true],
        ];

        foreach ($niveles as $nivel) {
            NivelAcademico::firstOrCreate(
                ['siglas' => $nivel['siglas']], // Buscar por siglas para evitar duplicados
                $nivel
            );
        }

        // 2. Sembrar Especialidades / Menciones
        $especialidades = [
            // Menciones específicas
            ['nombre' => 'Informática',         'descripcion' => 'Área de tecnología y desarrollo de software.',              'activo' => true],
            ['nombre' => 'Administración',       'descripcion' => 'Gestión empresarial y finanzas.',                          'activo' => true],
            ['nombre' => 'Análisis de Sistemas', 'descripcion' => 'Diseño, análisis y arquitectura de sistemas de información.', 'activo' => true],
            // Menciones generales (sin especialización específica)
            ['nombre' => 'Informática General',         'descripcion' => 'Mención general del área de Informática sin especialización particular.',         'activo' => true],
            ['nombre' => 'Administración General',       'descripcion' => 'Mención general del área de Administración sin especialización particular.',       'activo' => true],
            ['nombre' => 'Análisis de Sistemas General', 'descripcion' => 'Mención general del área de Análisis de Sistemas sin especialización particular.', 'activo' => true],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(
                ['nombre' => $especialidad['nombre']], // Buscar por nombre
                $especialidad
            );
        }
    }
}
