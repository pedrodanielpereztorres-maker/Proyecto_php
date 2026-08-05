<?php

namespace App\Filament\Resources\NivelesAcademicos\Pages;

use App\Filament\Resources\NivelesAcademicos\NivelAcademicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNivelesAcademicos extends ListRecords
{
    protected static string $resource = NivelAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Nivel Académico'),
        ];
    }
}
