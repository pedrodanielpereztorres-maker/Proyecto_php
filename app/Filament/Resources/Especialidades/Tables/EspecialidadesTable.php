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
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ]);
    }
}
