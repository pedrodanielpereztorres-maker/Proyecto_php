<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use App\Models\Profesor;
use App\Models\Semestre;
use App\Models\Horario;

class HorarioProfesores extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Horario de Profesores';
    protected static ?string $title = 'Horario de Profesores';

    protected string $view = 'filament.pages.horario-profesores';

    public ?int $profesor_id = null;
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
                Select::make('profesor_id')
                    ->label('Profesor')
                    ->options(
                        Profesor::orderBy('apellido')->get()
                            ->mapWithKeys(fn ($p) => [$p->id => "{$p->apellido}, {$p->nombre}"])
                    )
                    ->reactive()
                    ->placeholder('Seleccionar profesor'),
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->profesor_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'aula', 'semestre'])
            ->where('profesor_id', $this->profesor_id)
            ->when($this->semestre_id, fn ($q) => $q->where('semestre_id', $this->semestre_id))
            ->orderByRaw("CASE dia_semana
                WHEN 'Lunes' THEN 1
                WHEN 'Martes' THEN 2
                WHEN 'Miércoles' THEN 3
                WHEN 'Jueves' THEN 4
                WHEN 'Viernes' THEN 5
                WHEN 'Sábado' THEN 6
                ELSE 7 END")
            ->orderBy('hora_inicio')
            ->get();
    }
}
