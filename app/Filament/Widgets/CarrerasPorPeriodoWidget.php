<?php

namespace App\Filament\Widgets;

use App\Models\PeriodoAcademico;
use App\Models\Seccion;
use Filament\Widgets\BarChartWidget;

class CarrerasPorPeriodoWidget extends BarChartWidget
{
    protected ?string $heading = 'Secciones por carrera';

    protected ?string $description = 'Distribución real de la carga académica por carrera';

    protected string $color = 'primary';

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $periodoActivo = PeriodoAcademico::query()->latest('fecha_inicio')->first();

        $query = Seccion::query()
            ->with('carrera')
            ->whereNotNull('carrera_id');

        if ($periodoActivo) {
            $query->where('periodo_academico_id', $periodoActivo->id);
        }

        $datos = $query->get()->groupBy(fn ($seccion) => $seccion->carrera?->nombre ?? 'Sin carrera');

        $labels = [];
        $values = [];
        $colores = ['#c71b04', '#ea580c', '#f59e0b', '#10b981', '#3b82f6', '#7c3aed'];

        foreach ($datos as $nombre => $grupo) {
            $labels[] = $nombre;
            $values[] = $grupo->count();
        }

        return [
            'datasets' => [[
                'label' => 'Secciones',
                'data' => $values,
                'backgroundColor' => array_slice($colores, 0, max(1, count($values))),
                'borderColor' => '#ffffff',
                'borderWidth' => 1,
                'borderRadius' => 12,
                'borderSkipped' => false,
                'barPercentage' => 0.8,
                'categoryPercentage' => 0.7,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.18)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
