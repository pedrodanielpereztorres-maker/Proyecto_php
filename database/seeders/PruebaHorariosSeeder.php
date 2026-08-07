<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PeriodoAcademico;
use App\Models\Turno;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Seccion;
use App\Models\Espacio;
use App\Models\TipoEspacio;
use App\Models\NivelAcademico;
use App\Models\Especialidad;

class PruebaHorariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Período y Turno
        $periodo = PeriodoAcademico::firstOrCreate(
            ['codigo' => 'PR26-2'],
            ['nombre' => 'Periodo Regular 2026-2', 'estado' => 'planificacion', 'fecha_inicio' => '2026-09-01', 'fecha_fin' => '2026-12-15']
        );

        $turno = Turno::firstOrCreate(
            ['nombre' => 'Mañana']
        );

        // 2. Carrera
        $carrera = Carrera::firstOrCreate(
            ['codigo' => 'AS'],
            ['nombre' => 'Análisis de Sistemas']
        );

        // 3. Materias (Basado en el pensum y las horas)
        $materias = [
            ['codigo' => 'AS323', 'nombre' => 'Base de Datos', 'semestre' => 3, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'AS511', 'nombre' => 'Análisis de Sistemas III', 'semestre' => 5, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'AS512', 'nombre' => 'Ética', 'semestre' => 5, 'creditos' => 2, 'horas_teoricas' => 2, 'horas_practicas' => 0],
            ['codigo' => 'AS513', 'nombre' => 'Inteligencia Artificial', 'semestre' => 5, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
            ['codigo' => 'AS514', 'nombre' => 'Programación IV', 'semestre' => 5, 'creditos' => 3, 'horas_teoricas' => 2, 'horas_practicas' => 2],
        ];

        foreach ($materias as $mat) {
            Materia::firstOrCreate(
                ['codigo' => $mat['codigo']],
                [
                    'nombre' => $mat['nombre'],
                    'carrera_id' => $carrera->id,
                    'semestre' => $mat['semestre'],
                    'creditos' => $mat['creditos'],
                    'horas_teoricas' => $mat['horas_teoricas'],
                    'horas_practicas' => $mat['horas_practicas'],
                    'horas_semanales' => $mat['horas_teoricas'] + $mat['horas_practicas'],
                ]
            );
        }

        // 4. Secciones (SM3 para Semestre 3, SM5 para Semestre 5)
        Seccion::firstOrCreate(
            ['codigo' => 'SM3'],
            [
                'periodo_academico_id' => $periodo->id,
                'turno_id' => $turno->id,
                'carrera_id' => $carrera->id,
                'semestre' => 3,
                'cantidad_alumnos' => 30,
            ]
        );

        Seccion::firstOrCreate(
            ['codigo' => 'SM5'],
            [
                'periodo_academico_id' => $periodo->id,
                'turno_id' => $turno->id,
                'carrera_id' => $carrera->id,
                'semestre' => 5,
                'cantidad_alumnos' => 25,
            ]
        );

        // 5. Docente
        $nivel = NivelAcademico::firstOrCreate(['nombre' => 'Ingeniero']);
        $especialidad = Especialidad::firstOrCreate(['nombre' => 'Sistemas']);

        Profesor::firstOrCreate(
            ['cedula' => '00524'],
            [
                'nombre' => 'JONATHAN GABRIEL',
                'apellido' => 'MUNOZ TORRES',
                'direccion' => 'Acarigua',
                'telefono' => '0412-0000000',
                'email' => 'jonathan@example.com',
                'nivel_academico_id' => $nivel->id,
                'especialidad_id' => $especialidad->id,
                'codigo_interno' => '00524',
            ]
        );

        // 6. Espacios
        $tipoLab = TipoEspacio::firstOrCreate(['nombre' => 'Laboratorio']);
        $tipoAula = TipoEspacio::firstOrCreate(['nombre' => 'Aula Regular']);

        Espacio::firstOrCreate(['codigo' => 'LAB-1'], ['nombre' => 'Laboratorio de Computación 1', 'tipo_espacio_id' => $tipoLab->id, 'capacidad_maxima' => 30]);
        Espacio::firstOrCreate(['codigo' => 'AULA-15'], ['nombre' => 'Aula 15', 'tipo_espacio_id' => $tipoAula->id, 'capacidad_maxima' => 40]);
    }
}
