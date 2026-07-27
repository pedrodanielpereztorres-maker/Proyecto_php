<?php

namespace App\Filament\Resources\Turnos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TurnoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre del Turno')
                    ->placeholder('Ej. Matutino, Vespertino, Nocturno')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}