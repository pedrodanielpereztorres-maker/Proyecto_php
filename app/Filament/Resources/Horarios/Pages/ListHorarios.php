<?php

namespace App\Filament\Resources\Horarios\Pages;

use App\Filament\Resources\Horarios\HorarioResource;
use App\Models\JornadaParametro;
use App\Services\BloqueHorarioService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListHorarios extends ListRecords
{
    protected static string $resource = HorarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generarBloques')
                ->label('Generar Bloques Horarios')
                ->icon('heroicon-o-cog')
                ->requiresConfirmation()
                ->action(function (BloqueHorarioService $service): void {
                    $parametro = JornadaParametro::first();

                    if (! $parametro) {
                        Notification::make()
                            ->warning()
                            ->title('No hay jornada configurada')
                            ->send();

                        return;
                    }

                    $service->generarBloques($parametro, true);

                    Notification::make()
                        ->success()
                        ->title('Bloques generados')
                        ->send();
                }),
            CreateAction::make()
                ->label('Nuevo Horario'),
        ];
    }
}
