<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profesors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProfesorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cedula')
                    ->label('Cédula')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nombre_completo')
                    ->label('Docente')
                    ->state(fn ($record) => "{$record->apellido}, {$record->nombre}")
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-user')
                    ->searchable(['nombre', 'apellido'])
                    ->sortable(['apellido', 'nombre']),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->placeholder('Sin teléfono'),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('nivelAcademico.nombre')
                    ->label('Nivel')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('especialidad.nombre')
                    ->label('Especialidad')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('nivel_academico_id')
                    ->label('Nivel Académico')
                    ->native(false)
                    ->options(fn () => \App\Models\NivelAcademico::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
                SelectFilter::make('especialidad_id')
                    ->label('Especialidad')
                    ->native(false)
                    ->options(fn () => \App\Models\Especialidad::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray()),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('pdf')
                    ->label('Horario PDF')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->action(function ($record, $livewire) {
                        $tieneClases = \App\Models\Horario::where('profesor_id', $record->id)->exists();
                        if (!$tieneClases) {
                            \Filament\Notifications\Notification::make()
                                ->title('Docente sin Carga Horaria')
                                ->body("El profesor {$record->nombre} {$record->apellido} aún no tiene ningún horario de clases asignado.")
                                ->warning()
                                ->icon('heroicon-o-exclamation-triangle')
                                ->send();
                            return;
                        }
                        $url = route('profesores.pdf', ['profesor_id' => $record->id]);
                        $livewire->js("window.open('{$url}', '_blank')");
                    }),

                \Filament\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                    ->color('success')
                    ->action(function ($record, $livewire) {
                        // 1. Validar teléfono
                        $telefonoLimpio = preg_replace('/[^0-9]/', '', (string) $record->telefono);
                        if (empty($telefonoLimpio)) {
                            \Filament\Notifications\Notification::make()
                                ->title('Sin teléfono registrado')
                                ->body("El profesor {$record->nombre} {$record->apellido} no tiene un número telefónico asignado en su ficha.")
                                ->warning()
                                ->send();
                            return;
                        }

                        // Prefijo 58 de Venezuela
                        if (str_starts_with($telefonoLimpio, '0')) {
                            $telefonoLimpio = '58' . substr($telefonoLimpio, 1);
                        } elseif (!str_starts_with($telefonoLimpio, '58') && strlen($telefonoLimpio) === 10) {
                            $telefonoLimpio = '58' . $telefonoLimpio;
                        }

                        // 2. Validar horarios
                        $horarios = \App\Models\Horario::with(['materia', 'seccion', 'espacio', 'periodoAcademico'])
                            ->where('profesor_id', $record->id)
                            ->where('es_receso', false)
                            ->orderBy('dia_semana')
                            ->orderBy('hora_inicio')
                            ->get();

                        if ($horarios->isEmpty()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Docente sin Carga Horaria')
                                ->body("El profesor {$record->nombre} {$record->apellido} no tiene clases asignadas para enviar por WhatsApp.")
                                ->warning()
                                ->send();
                            return;
                        }

                        $periodo = $horarios->first()?->periodoAcademico?->codigo ?? 'Actual';
                        $pdfUrl = route('profesores.pdf', ['profesor_id' => $record->id]);

                        // Construir mensaje estructurado para WhatsApp
                        $texto = "🎓 *IUTEPI - Horario Académico ({$periodo})*\n\n";
                        $texto .= "Estimado(a) Prof. *{$record->nombre} {$record->apellido}*,\n";
                        $texto .= "Le compartimos el detalle de su carga horaria asignada:\n\n";

                        foreach ($horarios as $h) {
                            $materia = $h->materia->nombre ?? 'Materia';
                            $dia = $h->dia_semana;
                            $inicio = \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A');
                            $fin = \Carbon\Carbon::parse($h->hora_fin)->format('h:i A');
                            $sec = $h->seccion->codigo ?? 'N/A';
                            $aula = $h->espacio->codigo ?? 'N/A';
                            $texto .= "📌 *{$materia}*\n   📅 {$dia}: {$inicio} - {$fin}\n   🏫 Aula: {$aula} | Sec: {$sec}\n\n";
                        }

                        $texto .= "📄 *Descargar Horario Oficial en PDF:* \n{$pdfUrl}\n\n";
                        $texto .= "_Coordinación Académica IUTEPI_";

                        $waUrl = "https://api.whatsapp.com/send?phone={$telefonoLimpio}&text=" . urlencode($texto);
                        $livewire->js("window.open('{$waUrl}', '_blank')");
                    }),

                EditAction::make()
                    ->label('Editar'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
                ]),
            ]);
    }
}
