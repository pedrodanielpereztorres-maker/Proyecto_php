<?php

namespace App\Filament\Resources\Departamentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DepartamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Departamento')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Sistemas')
                            ->prefixIcon('heroicon-m-building-office-2'),
                        Textarea::make('descripcion')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Autoridades y Firmas (Para PDFs)')
                    ->description('Configura los datos y firmas de la autoridad de este departamento para los reportes.')
                    ->icon('heroicon-o-pencil-square')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre_coordinador')
                            ->label('Nombre del Coordinador/Jefe')
                            ->placeholder('Ej: Ing. Juan Pérez')
                            ->prefixIcon('heroicon-m-user')
                            ->maxLength(255),
                        TextInput::make('cedula_coordinador')
                            ->label('Cédula del Coordinador/Jefe')
                            ->placeholder('Ej: V-12.345.678')
                            ->prefixIcon('heroicon-m-identification')
                            ->maxLength(255),
                        FileUpload::make('firma_coordinador')
                            ->label('Firma Digital')
                            ->helperText('Se usará en los horarios emitidos por este departamento (PNG con fondo transparente).')
                            ->image()
                            ->directory('firmas'),
                        FileUpload::make('sello_departamento')
                            ->label('Sello del Departamento')
                            ->helperText('Sello oficial que acompañará a la firma (PNG con fondo transparente).')
                            ->image()
                            ->directory('sellos'),
                    ]),
            ]);
    }
}
