<?php

namespace App\Filament\Resources\TipoEspacios\Pages;

use App\Filament\Resources\TipoEspacios\TipoEspacioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoEspacios extends ListRecords
{
    protected static string $resource = TipoEspacioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
