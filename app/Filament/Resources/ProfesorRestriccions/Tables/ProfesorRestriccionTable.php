<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfesorRestriccions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfesorRestriccionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('profesor.nombre')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dia_semana')
                    ->sortable(),
                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->dateTime('H:i')
                    ->sortable(),
                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->dateTime('H:i')
                    ->sortable(),
                TextColumn::make('motivo')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}
