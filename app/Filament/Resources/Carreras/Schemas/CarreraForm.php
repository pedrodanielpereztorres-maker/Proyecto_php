<?php

declare(strict_types=1);

namespace App\Filament\Resources\Carreras\Schemas;

use App\Models\Departamento;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CarreraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la carrera')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columns(2),
                Section::make('Clasificación académica')
                    ->schema([
                        Select::make('nivel_academico_id')
                            ->label('Nivel Académico')
                            ->relationship('nivelAcademico', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->options(Departamento::query()->pluck('nombre', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }
}
