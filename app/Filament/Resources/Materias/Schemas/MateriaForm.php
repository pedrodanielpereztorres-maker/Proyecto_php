<?php

namespace App\Filament\Resources\Materias\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MateriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('creditos')
                    ->required()
                    ->numeric()
                    ->default(3),
                Select::make('carrera_id')
                    ->relationship('carrera', 'nombre')
                    ->label('Carrera')
                    ->nullable(),
                TextInput::make('horas_teoricas')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $set('horas_semanales', intval($get('horas_teoricas')) + intval($get('horas_practicas')));
                    })
                    ->label('Horas Teóricas (HT)'),
                TextInput::make('horas_practicas')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $set('horas_semanales', intval($get('horas_teoricas')) + intval($get('horas_practicas')));
                    })
                    ->label('Horas Prácticas (HP)'),
                TextInput::make('horas_semanales')
                    ->required()
                    ->numeric()
                    ->integer()
                    ->default(2)
                    ->readOnly()
                    ->label('Horas Semanales (Total)'),
                Select::make('semestre')
                    ->required()
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                        6 => '6',
                    ])
                    ->default(1)
                    ->label('Semestre'),
            ]);
    }
}
