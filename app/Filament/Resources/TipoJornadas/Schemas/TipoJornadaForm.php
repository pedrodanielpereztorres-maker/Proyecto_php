<?php

namespace App\Filament\Resources\TipoJornadas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TipoJornadaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
            ]);
    }
}
