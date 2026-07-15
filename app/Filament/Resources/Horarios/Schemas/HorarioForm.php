<?php

namespace App\Filament\Resources\Horarios\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class HorarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('materia_id')
                    ->relationship('materia', 'nombre')
                    ->required(),
                \Filament\Forms\Components\Select::make('profesor_id')
                    ->relationship('profesor', 'nombre')
                    ->required(),
                \Filament\Forms\Components\Select::make('aula_id')
                    ->relationship('aula', 'codigo')
                    ->required(),
                \Filament\Forms\Components\Select::make('dia_semana')
                    ->options([
                        'Lunes' => 'Lunes',
                        'Martes' => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves' => 'Jueves',
                        'Viernes' => 'Viernes',
                        'Sábado' => 'Sábado',
                    ])
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->required(),
                TimePicker::make('hora_fin')
                    ->required(),
            ]);
    }
}
