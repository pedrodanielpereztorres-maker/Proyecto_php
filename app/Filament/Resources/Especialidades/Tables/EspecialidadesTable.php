<?php

declare(strict_types=1);

namespace App\Filament\Resources\Especialidades\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EspecialidadesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Especialidad')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-o-sparkles'),
                    
                TextColumn::make('carrera.nombre')
                    ->label('Carrera Asociada')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-academic-cap')
                    ->placeholder('Sin Carrera'),
                    
                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->alignCenter()
                    ->tooltip('¿Está disponible para asignarse a docentes?'),
                    
                TextColumn::make('created_at')
                    ->label('Fecha de Creación')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nombre', 'asc');
    }
}
