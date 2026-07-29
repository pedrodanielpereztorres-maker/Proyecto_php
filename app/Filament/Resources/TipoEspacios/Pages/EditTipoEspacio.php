<?php

namespace App\Filament\Resources\TipoEspacios\Pages;

use App\Filament\Resources\TipoEspacios\TipoEspacioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTipoEspacio extends EditRecord
{
    protected static string $resource = TipoEspacioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
