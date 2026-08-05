<?php

declare(strict_types=1);

namespace App\Filament\Resources\Seccions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SeccionTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('periodoAcademico.codigo')
                    ->label('Período')
                    ->sortable(),
                TextColumn::make('turno.nombre')
                    ->label('Turno')
                    ->sortable(),
                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cantidad_alumnos')
                    ->label('Alumnos')
                    ->sortable(),
                BadgeColumn::make('semestre')
                    ->label('Semestre')
                    ->colors([
                        'primary',
                        'secondary',
                    ]),
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
