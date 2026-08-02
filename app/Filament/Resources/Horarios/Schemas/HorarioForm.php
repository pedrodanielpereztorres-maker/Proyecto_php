<?php

namespace App\Filament\Resources\Horarios\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use App\Models\PeriodoAcademico;

class HorarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('semestre_id')
                    ->label('Período Académico')
                    ->options(PeriodoAcademico::orderBy('codigo', 'desc')->pluck('codigo', 'id'))
                    ->default(fn () => PeriodoAcademico::where('estado', 'curso')->orWhere('estado', 'planificacion')->value('id'))
                    ->required(),
                Select::make('materia_id')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('profesor_id')
                    ->label('Profesor')
                    ->relationship('profesor', 'nombre')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('espacio_id')
                    ->label('Espacio')
                    ->relationship('espacio', 'codigo')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('dia_semana')
                    ->label('Día de la Semana')
                    ->options([
                        'Lunes'     => 'Lunes',
                        'Martes'    => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves'    => 'Jueves',
                        'Viernes'   => 'Viernes',
                        'Sábado'    => 'Sábado',
                    ])
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->label('Hora de Inicio')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('hora_fin')
                    ->label('Hora de Fin')
                    ->seconds(false)
                    ->required(),
            ]);
    }
}
