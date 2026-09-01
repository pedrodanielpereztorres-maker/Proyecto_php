<?php

namespace App\Filament\Widgets;

use App\Models\Carrera;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\PeriodoAcademico;
use App\Models\Profesor;
use App\Models\Seccion;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResumenGeneralWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Resumen general';

    protected ?string $description = 'Datos en tiempo real del sistema académico';

    protected function getStats(): array
    {
        $periodoActivo = PeriodoAcademico::query()->latest('fecha_inicio')->first();
        $periodos = PeriodoAcademico::query()->orderBy('fecha_inicio')->limit(6)->get();

        $seriePeriodos = $periodos->map(function ($periodo) {
            return Seccion::query()->where('periodo_academico_id', $periodo->id)->count();
        })->values()->all();

        $totalSecciones = Seccion::query()->count();
        $totalMaterias = Materia::query()->count();
        $totalProfesores = Profesor::query()->count();
        $totalCarreras = Carrera::query()->count();
        $totalAlumnos = (int) Seccion::query()->sum('cantidad_alumnos');
        $totalHorarios = Horario::query()->count();
        $seccionesConHorario = Seccion::query()->whereHas('horarios')->count();

        return [
            Stat::make('Secciones', $totalSecciones)
                ->description('registradas en total')
                ->descriptionIcon('heroicon-m-rectangle-group', IconPosition::Before)
                ->color('info')
                ->icon('heroicon-o-rectangle-group')
                ->chart($seriePeriodos ?: [0, 0, 0, 0]),

            Stat::make('Materias', $totalMaterias)
                ->description('en el catálogo')
                ->descriptionIcon('heroicon-m-book-open', IconPosition::Before)
                ->color('success')
                ->icon('heroicon-o-book-open')
                ->chart([12, 18, 24, 40, 70, 108]),

            Stat::make('Profesores', $totalProfesores)
                ->description('activos')
                ->descriptionIcon('heroicon-m-user-group', IconPosition::Before)
                ->color('warning')
                ->icon('heroicon-o-user-group')
                ->chart([5, 9, 13, 17, 19, 21]),

            Stat::make('Alumnos', $totalAlumnos)
                ->description('cobertura estimada')
                ->descriptionIcon('heroicon-m-users', IconPosition::Before)
                ->color('primary')
                ->icon('heroicon-o-users')
                ->chart([120, 150, 190, 240, 300, 360]),

            Stat::make('Horarios', $totalHorarios)
                ->description($periodoActivo ? 'periodo activo: ' . $periodoActivo->codigo : 'sin período')
                ->descriptionIcon('heroicon-m-calendar-days', IconPosition::Before)
                ->color('danger')
                ->icon('heroicon-o-calendar-days')
                ->chart([120, 180, 240, 300, 350, 413]),

            Stat::make('Carreras', $totalCarreras)
                ->description($seccionesConHorario . ' secciones con horario')
                ->descriptionIcon('heroicon-m-academic-cap', IconPosition::Before)
                ->color('gray')
                ->icon('heroicon-o-academic-cap')
                ->chart([1, 2, 2, 3, 3, 3]),
        ];
    }
}
