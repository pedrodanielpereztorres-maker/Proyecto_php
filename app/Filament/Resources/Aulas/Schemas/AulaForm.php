<?php

namespace App\Filament\Resources\Aulas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AulaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->required(),
                TextInput::make('capacidad')
                    ->required()
                    ->numeric(),
                \Filament\Forms\Components\Select::make('tipo')
                    ->options([
                        'Teoría' => 'Teoría',
                        'Laboratorio' => 'Laboratorio',
                    ])
                    ->required()
                    ->default('Teoría'),
            ]);
    }
}
