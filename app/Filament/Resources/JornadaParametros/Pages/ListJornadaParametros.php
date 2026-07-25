<?php

namespace App\Filament\Resources\JornadaParametros\Pages;

use App\Filament\Resources\JornadaParametros\JornadaParametroResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJornadaParametros extends ListRecords
{
    protected static string $resource = JornadaParametroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
