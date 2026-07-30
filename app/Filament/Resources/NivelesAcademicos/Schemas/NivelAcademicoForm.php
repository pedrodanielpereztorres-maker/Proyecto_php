<?php

declare(strict_types=1);

namespace App\Filament\Resources\NivelesAcademicos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NivelAcademicoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del nivel académico')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('siglas')
                        ->label('Siglas')
                        ->required()
                        ->maxLength(20),
                    Toggle::make('activo')
                        ->label('Activo')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
