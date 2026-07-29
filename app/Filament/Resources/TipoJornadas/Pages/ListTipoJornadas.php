<?php

namespace App\Filament\Resources\TipoJornadas\Pages;

use App\Filament\Resources\TipoJornadas\TipoJornadaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTipoJornadas extends ListRecords
{
    protected static string $resource = TipoJornadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
