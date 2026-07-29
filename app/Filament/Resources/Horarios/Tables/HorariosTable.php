<?php

namespace App\Filament\Resources\Horarios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Semestre;

class HorariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semestre.nombre')
                    ->label('Semestre')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dia_semana')
                    ->label('Día')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('profesor.nombre')
                    ->label('Profesor')
                    ->formatStateUsing(fn ($state, $record) =>
                        ($record->profesor?->nombre ?? '') . ' ' . ($record->profesor?->apellido ?? '')
                    )
                    ->searchable(),
                TextColumn::make('aula.codigo')
                    ->label('Aula')
                    ->badge()
                    ->sortable(),
                TextColumn::make('materia.carrera.nombre')
                    ->label('Carrera')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('semestre_id')
                    ->label('Semestre')
                    ->options(Semestre::orderBy('nombre', 'desc')->pluck('nombre', 'id')),
                SelectFilter::make('dia_semana')
                    ->label('Día')
                    ->options([
                        'Lunes'     => 'Lunes',
                        'Martes'    => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves'    => 'Jueves',
                        'Viernes'   => 'Viernes',
                        'Sábado'    => 'Sábado',
                    ]),
            ])
            ->defaultSort('hora_inicio')
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
