<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\JornadaParametro;
use Carbon\Carbon;

class HorarioProfesores extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;
    protected static ?string $navigationLabel = 'Horario de Profesores';
    protected static ?string $title = 'Horario de Profesores';

    protected string $view = 'filament.pages.horario-profesores';

    public ?int $profesor_id = null;
    public ?int $periodo_academico_id = null;
    public array $bloques = [];

    public function mount()
    {
        $this->cargarBloques();
    }

    public function cargarBloques()
    {
        $parametro = JornadaParametro::first();
        if (!$parametro) {
            return;
        }
        
        $inicio = Carbon::parse($parametro->hora_inicio);
        $fin = Carbon::parse($parametro->hora_fin);
        $this->bloques = [];

        // Generar grilla: bloques de 40 min, excepto a las 12:00 PM que es un receso de 20 min
        while ($inicio->lt($fin)) {
            $esReceso = ($inicio->format('H:i') === '12:00');
            $duracion = $esReceso ? 20 : 40;

            $inicioStr = $inicio->format('H:i');
            $inicioAmpm = $inicio->format('h:i A');
            $inicio->addMinutes($duracion);

            $this->bloques[] = [
                'inicio' => $inicioStr,
                'inicio_ampm' => $inicioAmpm,
                'fin_ampm' => $inicio->format('h:i A'),
                'es_receso_default' => $esReceso
            ];
        }
    }

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('periodo_academico_id')
                    ->label('Período Académico')
                    ->options(\App\Models\PeriodoAcademico::orderBy('codigo', 'desc')->pluck('codigo', 'id'))
                    ->default(fn () => \App\Models\PeriodoAcademico::where('estado', 'curso')->orWhere('estado', 'planificacion')->value('id'))
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-m-calendar')
                    ->helperText('Período académico para consultar la carga.')
                    ->live()
                    ->placeholder('Seleccionar período...'),

                Select::make('profesor_id')
                    ->label('Docente / Profesor')
                    ->options(
                        Profesor::orderBy('apellido')->orderBy('nombre')->get()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => ($p->cedula ? "C.I. {$p->cedula} — " : "") . "{$p->apellido}, {$p->nombre}"
                            ])
                    )
                    ->getSearchResultsUsing(function (string $search): array {
                        return Profesor::query()
                            ->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('cedula', 'like', "%{$search}%")
                            ->orderBy('apellido')
                            ->orderBy('nombre')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($p) => [
                                $p->id => ($p->cedula ? "C.I. {$p->cedula} — " : "") . "{$p->apellido}, {$p->nombre}"
                            ])
                            ->toArray();
                    })
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->prefixIcon('heroicon-m-user')
                    ->helperText('Busca por nombre, apellido o número de cédula.')
                    ->live()
                    ->placeholder('Buscar por nombre o cédula...'),
            ]);
    }

    public function getHorariosProperty()
    {
        if (! $this->profesor_id) {
            return collect();
        }

        return Horario::with(['materia.carrera', 'espacio', 'periodoAcademico', 'seccion'])
            ->where('profesor_id', $this->profesor_id)
            ->when($this->periodo_academico_id, fn ($q) => $q->where('periodo_academico_id', $this->periodo_academico_id))
            ->orderByRaw("CASE dia_semana
                WHEN 'Lunes' THEN 1
                WHEN 'Martes' THEN 2
                WHEN 'Miércoles' THEN 3
                WHEN 'Jueves' THEN 4
                WHEN 'Viernes' THEN 5
                WHEN 'Sábado' THEN 6
                ELSE 7 END")
            ->orderBy('hora_inicio')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('whatsapp')
                ->label('Enviar por WhatsApp')
                ->color('success')
                ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
                ->action(function () {
                    $profesor = Profesor::find($this->profesor_id);
                    if (!$profesor) return;

                    $telefonoLimpio = preg_replace('/[^0-9]/', '', (string) $profesor->telefono);
                    if (empty($telefonoLimpio)) {
                        \Filament\Notifications\Notification::make()
                            ->title('Sin número de teléfono')
                            ->body("El profesor {$profesor->nombre} {$profesor->apellido} no tiene registrado un teléfono.")
                            ->warning()
                            ->send();
                        return;
                    }

                    if (str_starts_with($telefonoLimpio, '0')) {
                        $telefonoLimpio = '58' . substr($telefonoLimpio, 1);
                    } elseif (!str_starts_with($telefonoLimpio, '58') && strlen($telefonoLimpio) === 10) {
                        $telefonoLimpio = '58' . $telefonoLimpio;
                    }

                    $horarios = $this->getHorariosProperty();
                    if ($horarios->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Docente sin Carga Horaria')
                            ->body("El profesor no tiene clases asignadas en este período.")
                            ->warning()
                            ->send();
                        return;
                    }

                    $periodo = \App\Models\PeriodoAcademico::find($this->periodo_academico_id)?->codigo ?? 'Actual';
                    $pdfUrl = route('profesores.pdf', [
                        'profesor_id' => $this->profesor_id,
                        'periodo_academico_id' => $this->periodo_academico_id,
                    ]);

                    $texto = "🎓 *IUTEPI - Horario Académico ({$periodo})*\n\n";
                    $texto .= "Estimado(a) Prof. *{$profesor->nombre} {$profesor->apellido}*,\n";
                    $texto .= "Le compartimos el detalle de su carga horaria asignada:\n\n";

                    foreach ($horarios as $h) {
                        if ($h->es_receso) continue;
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
                    $this->js("window.open('{$waUrl}', '_blank')");
                })
                ->visible(fn (): bool => !empty($this->profesor_id)),

            \Filament\Actions\Action::make('imprimirPdf')
                ->label('Vista Previa / Imprimir PDF')
                ->color('danger')
                ->icon('heroicon-o-printer')
                ->action(function () {
                    $horarios = $this->getHorariosProperty();
                    if ($horarios->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('Docente sin Carga Horaria')
                            ->body("El profesor seleccionado no posee clases asignadas en este período.")
                            ->warning()
                            ->send();
                        return;
                    }

                    $url = route('profesores.pdf', [
                        'profesor_id' => $this->profesor_id,
                        'periodo_academico_id' => $this->periodo_academico_id,
                    ]);
                    $this->js("window.open('{$url}', '_blank')");
                })
                ->visible(fn (): bool => !empty($this->profesor_id)),
        ];
    }
}
