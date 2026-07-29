<?php

namespace App\Filament\Resources\PeriodoAcademicos\Pages;

use App\Filament\Resources\PeriodoAcademicos\PeriodoAcademicoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPeriodoAcademico extends EditRecord
{
    protected static string $resource = PeriodoAcademicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
