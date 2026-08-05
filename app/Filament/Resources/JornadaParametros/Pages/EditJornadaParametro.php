<?php

namespace App\Filament\Resources\JornadaParametros\Pages;

use App\Filament\Resources\JornadaParametros\JornadaParametroResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJornadaParametro extends EditRecord
{
    protected static string $resource = JornadaParametroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar Jornada'),
        ];
    }
}
