<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use App\Models\Aula;
use App\Models\Horario;

class DisponibilidadAulas extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Disponibilidad de Aulas';
    protected static ?string $title = 'Disponibilidad de Aulas';

    protected string $view = 'filament.pages.disponibilidad-aulas';

    public ?int $aula_id = null;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Select::make('aula_id')
                    ->label('Seleccionar Aula')
                    ->options(Aula::pluck('codigo', 'id'))
                    ->reactive()
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->aula_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'profesor'])
            ->where('aula_id', $this->aula_id)
            ->orderBy('hora_inicio')
            ->get();
    }
}
