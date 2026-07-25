<?php

namespace App\Filament\Resources\PeriodoAcademicos\Pages;

use App\Filament\Resources\PeriodoAcademicos\PeriodoAcademicoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeriodoAcademicos extends ListRecords
{
    protected static string $resource = PeriodoAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
