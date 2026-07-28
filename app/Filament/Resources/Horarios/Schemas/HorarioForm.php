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
                Select::make('periodo_academico_id')
                    ->label('Periodo Académico')
                    ->relationship('periodoAcademico', 'codigo')
                    ->preload()
                    ->searchable()
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
                Select::make('aula_id')
                    ->label('Aula')
                    ->relationship('aula', 'codigo')
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
