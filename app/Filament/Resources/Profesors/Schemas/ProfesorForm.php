<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profesors\Schemas;

use App\Models\Especialidad;
use App\Models\NivelAcademico;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfesorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('cedula')
                            ->required()
                            ->maxLength(32)
                            ->unique(ignoreRecord: true),
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(128),
                        TextInput::make('apellido')
                            ->required()
                            ->maxLength(128),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('codigo_interno')
                            ->label('Código interno')
                            ->unique(ignoreRecord: true)
                            ->maxLength(64),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(24),
                        Textarea::make('direccion')
                            ->label('Dirección')
                            ->maxLength(512)
                            ->rows(3),
                        Select::make('nivel_academico_id')
                            ->label('Nivel Académico')
                            ->options(fn () => NivelAcademico::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray())
                            ->searchable()
                            ->nullable(),
                        Select::make('especialidad_id')
                            ->label('Especialidad')
                            ->options(fn () => Especialidad::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray())
                            ->searchable()
                            ->nullable(),
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->image()
                            ->directory('profesores')
                            ->maxSize(1024)
                            ->nullable(),
                    ]),
            ]);
    }
}
