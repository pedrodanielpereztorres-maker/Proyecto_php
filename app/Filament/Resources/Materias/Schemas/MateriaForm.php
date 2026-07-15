<?php

namespace App\Filament\Resources\Materias\Schemas;

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
                \Filament\Forms\Components\Select::make('carrera_id')
                    ->relationship('carrera', 'nombre')
                    ->label('Carrera')
                    ->nullable(),
            ]);
    }
}
