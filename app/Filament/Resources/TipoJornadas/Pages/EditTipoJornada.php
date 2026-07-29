<?php

namespace App\Filament\Resources\TipoJornadas\Pages;

use App\Filament\Resources\TipoJornadas\TipoJornadaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoJornada extends EditRecord
{
    protected static string $resource = TipoJornadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
