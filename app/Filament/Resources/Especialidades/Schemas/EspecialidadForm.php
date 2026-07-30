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
                ->description('Información de la especialidad o mención académica.')
                ->icon('heroicon-o-sparkles')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('nombre')
                        ->label('Nombre de la especialidad')
                        ->placeholder('Ej: Análisis de Sistemas')
                        ->helperText('Nombre completo de la especialidad o mención.')
                        ->prefixIcon('heroicon-m-sparkles')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                        
                    Textarea::make('descripcion')
                        ->label('Descripción')
                        ->placeholder('Breve descripción del perfil (opcional)...')
                        ->helperText('Información adicional o perfil del egresado.')
                        ->rows(3)
                        ->columnSpanFull(),
                        
                    Select::make('carrera_id')
                        ->label('Carrera Asociada')
                        ->options(Carrera::query()->pluck('nombre', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->prefixIcon('heroicon-m-academic-cap')
                        ->helperText('Carrera madre a la que pertenece (si aplica).'),
                        
                    Toggle::make('activo')
                        ->label('Estado de operatividad')
                        ->helperText('Habilitar o deshabilitar esta especialidad para nuevas asignaciones.')
                        ->inline(false)
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }
}
