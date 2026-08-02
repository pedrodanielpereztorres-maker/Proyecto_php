<?php

namespace App\Filament\Resources\Espacios\Pages;

use App\Filament\Resources\Espacios\EspacioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEspacios extends ListRecords
{
    protected static string $resource = EspacioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
