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
                ->description('Información básica del grado de instrucción o profesión.')
                ->icon('heroicon-o-academic-cap')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre completo')
                        ->placeholder('Ej: Técnico Superior Universitario')
                        ->helperText('Nombre completo del grado o profesión.')
                        ->prefixIcon('heroicon-m-identification')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                        
                    TextInput::make('siglas')
                        ->label('Abreviatura (Siglas)')
                        ->placeholder('Ej: TSU')
                        ->helperText('Formato corto usado en reportes y horarios.')
                        ->prefixIcon('heroicon-m-tag')
                        ->required()
                        ->maxLength(20),
                        
                    Toggle::make('activo')
                        ->label('Estado de operatividad')
                        ->helperText('Habilitar o deshabilitar este nivel para nuevas asignaciones.')
                        ->inline(false)
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
