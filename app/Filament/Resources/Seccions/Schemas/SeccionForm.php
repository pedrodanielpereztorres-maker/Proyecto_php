<?php

declare(strict_types=1);

namespace App\Filament\Resources\Seccions\Schemas;

use App\Models\Carrera;
use App\Models\PeriodoAcademico;
use App\Models\Turno;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Select::make('periodo_academico_id')
                            ->label('Período Académico')
                            ->options(PeriodoAcademico::orderBy('codigo', 'desc')->pluck('codigo', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('turno_id')
                            ->label('Turno')
                            ->options(Turno::pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('carrera_id')
                            ->label('Carrera')
                            ->options(Carrera::pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('semestre')
                            ->label('Semestre')
                            ->required()
                            ->maxLength(32),
                        TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(32),
                        TextInput::make('cantidad_alumnos')
                            ->label('Cantidad de alumnos')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                    ]),
            ]);
    }
}
