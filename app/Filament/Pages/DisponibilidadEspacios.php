<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use App\Models\Espacio;
use App\Models\Horario;

class DisponibilidadEspacios extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Disponibilidad de Espacios';
    protected static ?string $title = 'Disponibilidad de Espacios';

    protected string $view = 'filament.pages.disponibilidad-espacios';

    public ?int $espacio_id = null;
    public ?int $periodo_academico_id = null;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Select::make('periodo_academico_id')
                    ->label('Período Académico')
                    ->options(\App\Models\PeriodoAcademico::orderBy('codigo', 'desc')->pluck('codigo', 'id'))
                    ->default(fn () => \App\Models\PeriodoAcademico::where('estado', 'curso')->orWhere('estado', 'planificacion')->value('id'))
                    ->reactive()
                    ->placeholder('Seleccionar período'),
                Select::make('espacio_id')
                    ->label('Espacio')
                    ->options(Espacio::pluck('codigo', 'id'))
                    ->reactive()
                    ->placeholder('Seleccionar espacio'),
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->espacio_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'profesor', 'periodoAcademico'])
            ->where('espacio_id', $this->espacio_id)
            ->when($this->periodo_academico_id, fn ($q) => $q->where('periodo_academico_id', $this->periodo_academico_id))
            ->orderBy('periodo_academico_id')
            ->orderByRaw("CASE dia_semana
                WHEN 'Lunes' THEN 1
                WHEN 'Martes' THEN 2
                WHEN 'Mi\u00e9rcoles' THEN 3
                WHEN 'Jueves' THEN 4
                WHEN 'Viernes' THEN 5
                WHEN 'S\u00e1bado' THEN 6
                ELSE 7 END")
            ->orderBy('hora_inicio')
            ->get();
    }
}
