<?php

namespace App\Filament\Resources\TipoEspacios\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoEspacioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }
}