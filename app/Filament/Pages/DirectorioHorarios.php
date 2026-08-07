<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use App\Models\Seccion;

class DirectorioHorarios extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Horarios Terminados';
    protected static ?string $title = 'Directorio de Horarios';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.directorio-horarios';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Seccion::query()
                    ->whereHas('horarios')
                    ->with(['carrera', 'periodoAcademico'])
            )
            ->columns([
                TextColumn::make('codigo')
                    ->label('Sección')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                TextColumn::make('periodoAcademico.codigo')
                    ->label('Período')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('semestre')
                    ->label('Semestre')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('verHorario')
                    ->label('Abrir en Generador')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (Seccion $record): string => route('filament.admin.pages.generador-horarios', [
                        'periodo_academico_id' => $record->periodo_academico_id,
                        'carrera_id' => $record->carrera_id,
                        'semestre' => $record->semestre,
                        'seccion_id' => $record->id,
                    ])),
                Action::make('descargarPdf')
                    ->label('Guardar como PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('success')
                    ->url(fn (Seccion $record): string => route('horarios.pdf', ['seccion_id' => $record->id]))
                    ->openUrlInNewTab(),
            ]);
    }
}
