<?php

namespace App\Filament\Resources\Semestres\Pages;

use App\Filament\Resources\Semestres\SemestreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSemestre extends EditRecord
{
    protected static string $resource = SemestreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
