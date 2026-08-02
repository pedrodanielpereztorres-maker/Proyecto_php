<?php

namespace App\Filament\Resources\Espacios\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class EspacioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Espacio')
                    ->description('Datos básicos del espacio físico')
                    ->icon('heroicon-m-information-circle')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('codigo')
                            ->label('Código del Espacio')
                            ->placeholder('Ej: A101, L02, T001')
                            ->helperText('Identificador único del espacio (ej: A101)')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->required()
                            ->unique(ignorable: true),

                        TextInput::make('nombre')
                            ->label('Nombre del Espacio')
                            ->placeholder('Ej: Aula de Teoría 101')
                            ->helperText('Nombre descriptivo del espacio físico')
                            ->prefixIcon('heroicon-m-document-text')
                            ->required()
                            ->unique(ignorable: true),

                        TextInput::make('capacidad_maxima')
                            ->label('Capacidad Máxima')
                            ->placeholder('Ej: 40')
                            ->helperText('Cantidad máxima de estudiantes que caben en el espacio')
                            ->prefixIcon('heroicon-m-users')
                            ->required()
                            ->numeric()
                            ->minValue(1),

                        Select::make('tipo_espacio_id')
                            ->label('Tipo de Espacio')
                            ->placeholder('Selecciona un tipo')
                            ->helperText('Clasificación del tipo de espacio (Laboratorio, Teoría, etc.)')
                            ->prefixIcon('heroicon-m-square-3-stack-3d')
                            ->relationship('tipoEspacio', 'nombre')
                            ->required()
                            ->native(false),

                        Select::make('estatus_operativo')
                            ->label('Estado Operativo')
                            ->options([
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo',
                                'mantenimiento' => 'En Mantenimiento',
                            ])
                            ->helperText('Estado actual del espacio')
                            ->prefixIcon('heroicon-m-check-circle')
                            ->required()
                            ->default('activo')
                            ->native(false),
                    ]),
            ]);
    }
}
