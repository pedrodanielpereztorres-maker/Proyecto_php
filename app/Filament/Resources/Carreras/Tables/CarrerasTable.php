<?php

declare(strict_types=1);

namespace App\Filament\Resources\Carreras\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CarrerasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nivelAcademico.nombre')
                    ->label('Nivel Académico')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->sortable()
                    ->searchable(),
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
