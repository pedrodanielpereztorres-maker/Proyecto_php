<?php

namespace App\Filament\Imports;

use App\Models\Materia;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class MateriaImporter extends Importer
{
    protected static ?string $model = Materia::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('codigo')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('nombre')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('creditos')
                ->rules(['integer']),
            ImportColumn::make('carrera_id')
                ->rules(['nullable', 'integer']),
            ImportColumn::make('horas_semanales')
                ->rules(['integer']),
            ImportColumn::make('semestre')
                ->rules(['integer', 'between:1,6']),
            ImportColumn::make('tipo_espacio_id')
                ->rules(['nullable', 'integer']),
        ];
    }

    public function resolveRecord(): ?Materia
    {
        return Materia::firstOrNew(['codigo' => $this->data['codigo']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Importacion completada: ' . number_format($import->successful_rows) . ' filas importadas.';
    }
}
