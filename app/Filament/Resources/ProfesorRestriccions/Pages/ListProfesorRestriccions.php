<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfesorRestriccions\Pages;

use App\Filament\Resources\ProfesorRestriccions\ProfesorRestriccionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProfesorRestriccions extends ListRecords
{
    protected static string $resource = ProfesorRestriccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Restricción Docente'),
        ];
    }
}
