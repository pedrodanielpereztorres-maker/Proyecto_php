<?php

namespace App\Filament\Resources\Turnos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TurnosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombre del Turno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}