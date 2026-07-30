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
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('siglas')
                    ->label('Siglas')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),
            ]);
    }
}
