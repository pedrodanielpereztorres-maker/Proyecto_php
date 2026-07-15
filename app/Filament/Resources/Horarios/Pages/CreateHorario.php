<?php

namespace App\Filament\Resources\Horarios\Pages;

use App\Filament\Resources\Horarios\HorarioResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateHorario extends CreateRecord
{
    protected static string $resource = HorarioResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::handleRecordCreation($data);
        } catch (ValidationException $e) {
            $mensaje = collect($e->errors())->flatten()->first();
            Notification::make()
                ->title('⚠️ Choque de Horario Detectado')
                ->body($mensaje)
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }
}
