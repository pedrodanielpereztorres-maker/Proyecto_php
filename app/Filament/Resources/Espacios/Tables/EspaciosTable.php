<?php

namespace App\Filament\Resources\Espacios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class EspaciosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-hashtag'),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-document-text'),

                TextColumn::make('capacidad_maxima')
                    ->label('Capacidad')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-m-users'),

                TextColumn::make('tipoEspacio.nombre')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                BadgeColumn::make('estatus_operativo')
                    ->label('Estado')
                    ->colors([
                        'success' => 'activo',
                        'danger' => 'inactivo',
                        'warning' => 'mantenimiento',
                    ])
                    ->icons([
                        'heroicon-m-check-circle' => 'activo',
                        'heroicon-m-x-circle' => 'inactivo',
                        'heroicon-m-exclamation-triangle' => 'mantenimiento',
                    ]),

                TextColumn::make('horarios_count')
                    ->label('Horarios Asignados')
                    ->counts('horarios')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
