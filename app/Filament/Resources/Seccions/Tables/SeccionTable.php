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
                    ->label('Sección')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-rectangle-stack')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('semestre')
                    ->label('Semestre')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "Sem. {$state}")
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('turno.nombre')
                    ->label('Turno')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('periodoAcademico.codigo')
                    ->label('Período')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('cantidad_alumnos')
                    ->label('Alumnos')
                    ->badge()
                    ->color(fn ($state) => empty($state) ? 'gray' : 'success')
                    ->formatStateUsing(fn ($state) => !empty($state) ? "{$state} est." : 'Sin definir')
                    ->alignCenter()
                    ->sortable(),
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
