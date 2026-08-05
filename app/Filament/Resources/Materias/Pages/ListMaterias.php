<?php

namespace App\Filament\Resources\Materias\Pages;

use App\Filament\Imports\MateriaImporter;
use App\Filament\Resources\Materias\MateriaResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterias extends ListRecords
{
    protected static string $resource = MateriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(MateriaImporter::class),
            CreateAction::make()
                ->label('Nueva Materia'),
        ];
    }
}
