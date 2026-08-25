<?php

namespace App\Filament\Resources\Materias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MateriasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-key')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre')
                    ->label('Asignatura')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->badge()
                    ->color('info')
                    ->placeholder('General / Tronco Común')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('semestre')
                    ->label('Semestre')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}° Sem" : 'N/A')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('horas_semanales')
                    ->label('Horas/Sem')
                    ->formatStateUsing(fn ($state) => "{$state}h")
                    ->badge()
                    ->color('warning')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('tipoEspacio.nombre')
                    ->label('Espacio Requerido')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-map-pin')
                    ->placeholder('No definido')
                    ->sortable(),

                TextColumn::make('creditos')
                    ->label('U.C.')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
