<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profesors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProfesorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cedula')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('apellido')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('codigo_interno')
                    ->label('Código interno')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('nivelAcademico.nombre')
                    ->label('Nivel Académico')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('especialidad.nombre')
                    ->label('Especialidad')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('nivel_academico_id')
                    ->label('Nivel Académico')
                    ->options(fn () => \App\Models\NivelAcademico::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                SelectFilter::make('especialidad_id')
                    ->label('Especialidad')
                    ->options(fn () => \App\Models\Especialidad::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
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
