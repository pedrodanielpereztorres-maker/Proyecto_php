<?php

declare(strict_types=1);

namespace App\Filament\Resources\Especialidades\Schemas;

use App\Models\Carrera;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EspecialidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos de la especialidad')
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpanFull(),
                    Select::make('carrera_id')
                        ->label('Carrera')
                        ->options(Carrera::query()->pluck('nombre', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Toggle::make('activo')
                        ->label('Activo')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
