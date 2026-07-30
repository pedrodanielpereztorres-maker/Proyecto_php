<?php

declare(strict_types=1);

namespace App\Filament\Resources\NivelesAcademicos\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NivelesAcademicosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nivel Académico')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-o-academic-cap'),
                    
                TextColumn::make('siglas')
                    ->label('Abreviatura')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->alignCenter()
                    ->tooltip('¿Está disponible para asignarse a nuevos docentes?'),
                    
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nombre', 'asc');
    }
}
