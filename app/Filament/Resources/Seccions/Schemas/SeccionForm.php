<?php

declare(strict_types=1);

namespace App\Filament\Resources\Seccions\Schemas;

use App\Models\Carrera;
use App\Models\PeriodoAcademico;
use App\Models\Turno;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SeccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Sección Académica')
                    ->description('Configura la carrera, período, turno, semestre y capacidad de estudiantes para esta sección.')
                    ->icon('heroicon-o-rectangle-stack')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('periodo_academico_id')
                            ->label('Período Académico')
                            ->options(PeriodoAcademico::orderBy('codigo', 'desc')->pluck('codigo', 'id'))
                            ->placeholder('Seleccionar período...')
                            ->helperText('Período lectivo al que pertenece la sección.')
                            ->prefixIcon('heroicon-m-calendar')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('turno_id')
                            ->label('Turno')
                            ->options(Turno::pluck('nombre', 'id'))
                            ->placeholder('Seleccionar turno...')
                            ->helperText('Turno de clases (Mañana, Tarde, Noche o Sabatino).')
                            ->prefixIcon('heroicon-m-sun')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('carrera_id')
                            ->label('Carrera')
                            ->options(Carrera::pluck('nombre', 'id'))
                            ->placeholder('Seleccionar carrera...')
                            ->helperText('Programa académico de la sección.')
                            ->prefixIcon('heroicon-m-academic-cap')
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('semestre')
                            ->label('Semestre')
                            ->placeholder('Ej: 3')
                            ->helperText('Nivel del pénsum (1 al 6).')
                            ->prefixIcon('heroicon-m-numbered-list')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->required(),

                        TextInput::make('codigo')
                            ->label('Código / Nomenclatura')
                            ->placeholder('Ej: SM3, SIS-501...')
                            ->helperText('Identificador único de la sección.')
                            ->prefixIcon('heroicon-m-tag')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(32),

                        TextInput::make('cantidad_alumnos')
                            ->label('Cantidad de Estudiantes (Opcional)')
                            ->placeholder('Ej: 25 (Dejar vacío si no se conoce)')
                            ->helperText('Opcional. Si aún no se conoce la matrícula real o la sección se dividirá en subgrupos prácticos, puede dejarse vacío.')
                            ->prefixIcon('heroicon-m-users')
                            ->numeric()
                            ->nullable()
                            ->minValue(0),
                    ]),
            ]);
    }
}
