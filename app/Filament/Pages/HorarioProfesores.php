<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use App\Models\Profesor;
use App\Models\Horario;

class HorarioProfesores extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Horario de Profesores';
    protected static ?string $title = 'Horario de Profesores';

    protected string $view = 'filament.pages.horario-profesores';

    public ?int $profesor_id = null;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Select::make('profesor_id')
                    ->label('Seleccionar Profesor')
                    ->options(Profesor::pluck('nombre', 'id'))
                    ->reactive()
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->profesor_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'aula'])
            ->where('profesor_id', $this->profesor_id)
            ->orderBy('hora_inicio')
            ->get();
    }
}
