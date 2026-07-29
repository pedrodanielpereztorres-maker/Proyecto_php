<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use App\Models\Aula;
use App\Models\Semestre;
use App\Models\Horario;

class DisponibilidadAulas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Disponibilidad de Aulas';
    protected static ?string $title = 'Disponibilidad de Aulas';

    protected string $view = 'filament.pages.disponibilidad-aulas';

    public ?int $aula_id = null;
    public ?int $semestre_id = null;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Select::make('semestre_id')
                    ->label('Semestre')
                    ->options(Semestre::orderBy('nombre', 'desc')->pluck('nombre', 'id'))
                    ->default(fn () => Semestre::where('activo', true)->value('id'))
                    ->reactive()
                    ->placeholder('Seleccionar semestre'),
                Select::make('aula_id')
                    ->label('Aula')
                    ->options(Aula::pluck('codigo', 'id'))
                    ->reactive()
                    ->placeholder('Seleccionar aula'),
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->aula_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'profesor', 'semestre'])
            ->where('aula_id', $this->aula_id)
            ->when($this->semestre_id, fn ($q) => $q->where('semestre_id', $this->semestre_id))
            ->orderBy('semestre_id')
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
