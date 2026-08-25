<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use App\Models\PeriodoAcademico;
use App\Models\Seccion;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\JornadaParametro;
use App\Services\BloqueHorarioService;
use Illuminate\Support\Carbon;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Livewire\Attributes\Url;

class GeneradorHorarios extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Armar Horario';
    protected static ?string $title = 'Generador de Horarios';
    protected static ?int $navigationSort = 1;
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';

    protected string $view = 'filament.pages.generador-horarios';

    #[Url]
    public ?int $periodo_academico_id = null;
    
    #[Url]
    public ?int $carrera_id = null;
    
    #[Url]
    public ?int $semestre = null;
    
    #[Url]
    public ?int $seccion_id = null;
    
    public array $bloques = [];
    public array $horariosAsignados = [];
    public array $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    public int $renderCount = 0;
    /** True si la sección seleccionada permite edición (estado 'borrador') */
    public bool $seccionEditable = false;

    public function mount()
    {
        $this->periodo_academico_id = PeriodoAcademico::query()->whereIn('estado', ['curso', 'planificacion'])->orderByDesc('id')->value('id');
        $this->cargarBloques();
        $this->cargarHorarios();
    }

    public function getSeccionNombreProperty()
    {
        return \App\Models\Seccion::find($this->seccion_id)?->codigo ?? '';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(4)
                    ->schema([
                        Select::make('periodo_academico_id')
                            ->label('Período')
                            ->options(PeriodoAcademico::pluck('codigo', 'id'))
                            ->live()
                            ->afterStateUpdated(fn () => $this->cargarHorarios()),
                        Select::make('carrera_id')
                            ->label('Carrera')
                            ->options(\App\Models\Carrera::pluck('nombre', 'id'))
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('seccion_id', null);
                                $this->cargarHorarios();
                            }),
                        Select::make('semestre')
                            ->label('Semestre')
                            ->options([1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6'])
                            ->live()
                            ->afterStateUpdated(function (callable $set) {
                                $set('seccion_id', null);
                                $this->cargarHorarios();
                            }),
                        Select::make('seccion_id')
                            ->label('Sección')
                            ->options(function (callable $get) {
                                $query = Seccion::query();
                                if ($get('periodo_academico_id')) $query->where('periodo_academico_id', $get('periodo_academico_id'));
                                if ($get('carrera_id')) $query->where('carrera_id', $get('carrera_id'));
                                if ($get('semestre')) $query->where('semestre', $get('semestre'));
                                return $query->pluck('codigo', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn () => $this->cargarHorarios()),
                    ])
            ]);
    }

    public function cargarBloques()
    {
        $parametro = \App\Models\JornadaParametro::first();
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

    public function cargarHorarios()
    {
        if (!$this->periodo_academico_id || !$this->seccion_id) {
            $this->horariosAsignados = [];
            return;
        }

        $horarios = Horario::with(['materia', 'profesor', 'espacio'])
            ->where('periodo_academico_id', $this->periodo_academico_id)
            ->where('seccion_id', $this->seccion_id)
            ->get();
            
        // Guardamos arrays planos (NO modelos Eloquent) para evitar
        // problemas de serialización de Livewire entre requests.
        $this->horariosAsignados = [];
        foreach ($horarios as $h) {
            if ($h->hora_inicio) {
                $horaInicioStr = Carbon::parse($h->hora_inicio)->format('H:i');
                $key = $h->dia_semana . '_' . $horaInicioStr;
                $this->horariosAsignados[$key] = [
                    'id'               => $h->id,
                    'materia_id'       => $h->materia_id,
                    'es_receso'        => (bool) $h->es_receso,
                    'hora_inicio'      => $horaInicioStr,
                    'hora_fin'         => Carbon::parse($h->hora_fin)->format('H:i'),
                    'materia_nombre'   => optional($h->materia)->nombre ?? '',
                    'profesor_nombre'  => optional($h->profesor)->nombre ?? '',
                    'profesor_apellido'=> optional($h->profesor)->apellido ?? '',
                    'espacio_codigo'   => optional($h->espacio)->codigo ?? '',
                ];
            }
        }

        // Actualizar flag de edición según el estado de la sección
        $seccion = \App\Models\Seccion::find($this->seccion_id);
        $this->seccionEditable = $seccion && $seccion->estado_horario === 'borrador';

        $this->renderCount++;
    }

    public function asignarBloqueAction(): Action
    {
        return Action::make('asignarBloque')
            ->label('Asignar Clase')
            ->iconButton()
            ->color('primary')
            ->size('sm')
            ->icon('heroicon-m-plus-circle')
            ->modalHeading('Asignar Clase al Horario')
            ->modalDescription('Complete los datos para asignar una clase a este bloque horario.')
            ->modalIcon('heroicon-o-academic-cap')
            ->modalWidth('5xl')
            ->form([
                \Filament\Schemas\Components\Section::make('Configuración de la Clase')
                    ->icon('heroicon-m-calendar-days')
                    ->columns(2)
                    ->schema([
                        \Filament\Forms\Components\Select::make('materia_id')
                            ->label('Materia / Asignatura')
                            ->placeholder('Buscar materia por nombre o código...')
                            ->helperText('Materias del semestre y carrera.')
                            ->prefixIcon('heroicon-m-book-open')
                            ->options(function () {
                                $query = \App\Models\Materia::query();
                                if ($this->carrera_id) {
                                    $query->where('carrera_id', $this->carrera_id);
                                }
                                if ($this->semestre) {
                                    $query->where('semestre', $this->semestre);
                                }
                                return $query->get()->mapWithKeys(fn ($m) =>
                                    [$m->id => "[{$m->codigo}] {$m->nombre}  ·  {$m->horas_semanales} hrs/sem"]
                                );
                            })
                            ->searchable()
                            ->native(false)
                            ->required(),

                        \Filament\Forms\Components\Select::make('cantidad_bloques')
                            ->label('Duración del Bloque')
                            ->placeholder('Seleccione duración...')
                            ->helperText('Cada bloque equivale a 40 minutos de clase.')
                            ->prefixIcon('heroicon-m-clock')
                            ->options([
                                1 => '1 Bloque (40 min)',
                                2 => '2 Bloques (1 h 20 min)',
                                3 => '3 Bloques (2 horas)',
                                4 => '4 Bloques (2 h 40 min)',
                            ])
                            ->default(1)
                            ->native(false)
                            ->required(),

                        \Filament\Forms\Components\Select::make('profesor_id')
                            ->label('Docente Responsable')
                            ->placeholder('Buscar profesor o cédula...')
                            ->helperText('Nombre y cédula del profesor.')
                            ->prefixIcon('heroicon-m-user')
                            ->options(
                                \App\Models\Profesor::orderBy('apellido')
                                    ->get()
                                    ->mapWithKeys(fn ($p) =>
                                        [$p->id => "{$p->apellido}, {$p->nombre}  ·  CI: {$p->cedula}"]
                                    )
                            )
                            ->searchable()
                            ->native(false)
                            ->required(),

                        \Filament\Forms\Components\Select::make('espacio_id')
                            ->label('Espacio / Aula')
                            ->placeholder('Buscar aula o laboratorio...')
                            ->helperText('Espacio físico disponible.')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->options(
                                \App\Models\Espacio::with('tipoEspacio')
                                    ->orderBy('codigo')
                                    ->get()
                                    ->mapWithKeys(fn ($e) =>
                                        [$e->id => "{$e->codigo}  ·  Cap: {$e->capacidad_maxima}  ·  " . optional($e->tipoEspacio)->nombre]
                                    )
                            )
                            ->searchable()
                            ->native(false)
                            ->required(),
                    ]),

                \Filament\Schemas\Components\Section::make('Vincular a otras secciones (Tronco Común / Multicarrera - Opcional)')
                    ->icon('heroicon-m-arrows-pointing-out')
                    ->schema([
                        \Filament\Forms\Components\Select::make('secciones_adicionales')
                            ->label('Secciones adicionales')
                            ->placeholder('Seleccionar secciones...')
                            ->helperText('Se creará este mismo bloque en las secciones seleccionadas de forma simultánea.')
                            ->prefixIcon('heroicon-m-rectangle-stack')
                            ->options(function () {
                                return Seccion::with(['carrera', 'turno'])
                                    ->where('periodo_academico_id', $this->periodo_academico_id)
                                    ->where('id', '!=', $this->seccion_id)
                                    ->when($this->semestre, fn ($q) => $q->where('semestre', $this->semestre))
                                    ->orderBy('carrera_id')
                                    ->orderBy('codigo')
                                    ->get()
                                    ->mapWithKeys(fn ($s) => [
                                        $s->id => "{$s->codigo} — " . (optional($s->carrera)->nombre ?? 'General') . ($s->turno ? " ({$s->turno->nombre})" : "")
                                    ]);
                            })
                            ->multiple()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ])
            ->action(function (array $data, array $arguments) {
                $dia = $arguments['dia'];
                $inicioStr = $arguments['inicio'];
                
                // Buscar el índice del bloque inicial
                $startIndex = -1;
                foreach ($this->bloques as $i => $b) {
                    if ($b['inicio'] === $inicioStr) {
                        $startIndex = $i;
                        break;
                    }
                }

                if ($startIndex !== -1) {
                    \Illuminate\Support\Facades\DB::beginTransaction();
                    try {
                        $minutosTotales = 0;
                        for ($j = 0; $j < $data['cantidad_bloques']; $j++) {
                            if (isset($this->bloques[$startIndex + $j])) {
                                $bloqueActual = $this->bloques[$startIndex + $j];
                                $duracion = $bloqueActual['es_receso_default'] ? 20 : 40;
                                $minutosTotales += $duracion;
                            }
                        }
                        
                        $bloqueInicial = $this->bloques[$startIndex];
                        $horaFinCalculada = \Carbon\Carbon::parse($bloqueInicial['inicio'])->addMinutes($minutosTotales)->format('H:i');
                        
                        $seccionesObjetivo = array_merge([$this->seccion_id], $data['secciones_adicionales'] ?? []);
                        $materiaBase = \App\Models\Materia::find($data['materia_id']);

                        foreach ($seccionesObjetivo as $secId) {
                            $materiaIdFinal = $data['materia_id'];
                            $secObj = Seccion::find($secId);

                            if ($secObj && $secId != $this->seccion_id && $materiaBase) {
                                $materiaEquiv = \App\Models\Materia::where('carrera_id', $secObj->carrera_id)
                                    ->where(function ($q) use ($materiaBase) {
                                        $q->where('codigo', $materiaBase->codigo)
                                          ->orWhere('nombre', $materiaBase->nombre);
                                    })
                                    ->first();
                                if ($materiaEquiv) {
                                    $materiaIdFinal = $materiaEquiv->id;
                                }
                            }

                            \App\Models\Horario::create([
                                'periodo_academico_id' => $this->periodo_academico_id,
                                'seccion_id' => $secId,
                                'materia_id' => $materiaIdFinal,
                                'profesor_id' => $data['profesor_id'],
                                'espacio_id' => $data['espacio_id'],
                                'dia_semana' => $dia,
                                'hora_inicio' => $bloqueInicial['inicio'],
                                'hora_fin' => $horaFinCalculada,
                                'es_receso' => false,
                                'omitir_validacion_capacidad' => true,
                            ]);
                        }
                        \Illuminate\Support\Facades\DB::commit();
                        $this->cargarHorarios();
                        \Filament\Notifications\Notification::make()->title('Bloques asignados correctamente')->success()->send();
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        \Illuminate\Support\Facades\DB::rollBack();
                        $errores = collect($e->errors())->flatten()->implode(', ');
                        \Filament\Notifications\Notification::make()
                            ->title('¡No se pudo asignar el bloque!')
                            ->body($errores)
                            ->warning()
                            ->icon('heroicon-o-exclamation-triangle')
                            ->duration(8000)
                            ->send();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\DB::rollBack();
                        \Filament\Notifications\Notification::make()
                            ->title('Error al asignar bloque')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }
            })
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Confirmar Asignación')
            ->modalCancelActionLabel('Cancelar')
            ->visible(fn () => $this->seccionEditable);
    }

    public function asignarRecesoAction(): Action
    {
        return Action::make('asignarReceso')
            ->label('Asignar Receso')
            ->iconButton()
            ->color('warning')
            ->size('sm')
            ->icon('heroicon-m-clock')
            ->action(function (array $arguments) {
                // Calcular el fin del receso (inicio + 20 mins)
                $inicio = Carbon::parse($arguments['inicio']);
                $finReceso = $inicio->copy()->addMinutes(20)->format('H:i');

                \App\Models\Horario::create([
                    'periodo_academico_id' => $this->periodo_academico_id,
                    'seccion_id' => $this->seccion_id,
                    'dia_semana' => $arguments['dia'],
                    'hora_inicio' => $arguments['inicio'],
                    'hora_fin' => $finReceso,
                    'es_receso' => true,
                ]);

                $this->cargarHorarios();
                \Filament\Notifications\Notification::make()->title('Receso asignado (20 min)')->success()->send();
            })
            ->visible(fn () => $this->seccionEditable);
    }

    public function eliminarBloqueAction(): Action
    {
        return Action::make('eliminarBloque')
            ->label('')
            ->iconButton()
            ->color('danger')
            ->size('sm')
            ->icon('heroicon-m-x-circle')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\Horario::find($arguments['id'])?->delete();
                $this->cargarHorarios();
                \Filament\Notifications\Notification::make()->title('Bloque eliminado')->success()->send();
            })
            ->visible(fn () => $this->seccionEditable);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clonarHorario')
                ->label('Clonar Horario')
                ->color('info')
                ->icon('heroicon-o-document-duplicate')
                ->modalHeading('Clonar Horario a Otra Sección')
                ->modalDescription('Copia todas las clases y recesos de esta sección hacia otra sección destino. Las materias comunes (mismo profesor y aula) se mantendrán compartidas sin choques.')
                ->modalIcon('heroicon-o-document-duplicate')
                ->form([
                    \Filament\Schemas\Components\Section::make('Configuración de Clonación')
                        ->schema([
                            \Filament\Forms\Components\Select::make('seccion_destino_id')
                                ->label('Sección Destino')
                                ->placeholder('Seleccionar sección destino...')
                                ->helperText('Selecciona la sección que recibirá la copia exacta de este horario.')
                                ->prefixIcon('heroicon-m-rectangle-stack')
                                ->options(function () {
                                    return Seccion::with(['carrera', 'turno'])
                                        ->where('periodo_academico_id', $this->periodo_academico_id)
                                        ->where('id', '!=', $this->seccion_id)
                                        ->when($this->semestre, fn ($q) => $q->where('semestre', $this->semestre))
                                        ->orderBy('carrera_id')
                                        ->orderBy('codigo')
                                        ->get()
                                        ->mapWithKeys(fn ($s) => [
                                            $s->id => "{$s->codigo} — " . (optional($s->carrera)->nombre ?? 'General') . " · Sem. {$s->semestre}" . ($s->turno ? " ({$s->turno->nombre})" : "")
                                        ]);
                                })
                                ->native(false)
                                ->searchable()
                                ->preload()
                                ->required(),
                            \Filament\Forms\Components\Toggle::make('reemplazar_existentes')
                                ->label('Reemplazar horario existente si la sección destino ya tiene clases')
                                ->default(false)
                                ->helperText('Si se activa, eliminará previamente los bloques asignados en la sección destino.'),
                        ])
                ])
                ->modalSubmitActionLabel('Clonar Horario Ahora')
                ->modalCancelActionLabel('Cancelar')
                ->action(function (array $data) {
                    $seccionDestinoId = $data['seccion_destino_id'];
                    $seccionDestino = Seccion::with('carrera')->find($seccionDestinoId);
                    $seccionOrigen = Seccion::with('carrera')->find($this->seccion_id);

                    if (!$seccionDestino || !$seccionOrigen) return;

                    $horariosOrigen = Horario::where('seccion_id', $this->seccion_id)
                        ->where('periodo_academico_id', $this->periodo_academico_id)
                        ->get();

                    if ($horariosOrigen->isEmpty()) {
                        \Filament\Notifications\Notification::make()
                            ->title('La sección actual no tiene clases para clonar.')
                            ->warning()
                            ->send();
                        return;
                    }

                    \Illuminate\Support\Facades\DB::beginTransaction();
                    try {
                        if (!empty($data['reemplazar_existentes'])) {
                            Horario::where('seccion_id', $seccionDestinoId)
                                ->where('periodo_academico_id', $this->periodo_academico_id)
                                ->delete();
                        }

                        $clonadas = 0;
                        foreach ($horariosOrigen as $h) {
                            $materiaDestinoId = $h->materia_id;
                            if ($h->materia && $seccionDestino->carrera_id !== $seccionOrigen->carrera_id) {
                                $materiaEquivalente = \App\Models\Materia::where('carrera_id', $seccionDestino->carrera_id)
                                    ->where(function ($q) use ($h) {
                                        $q->where('codigo', $h->materia->codigo)
                                          ->orWhere('nombre', $h->materia->nombre);
                                    })
                                    ->first();
                                if ($materiaEquivalente) {
                                    $materiaDestinoId = $materiaEquivalente->id;
                                }
                            }

                            Horario::create([
                                'periodo_academico_id' => $this->periodo_academico_id,
                                'seccion_id' => $seccionDestinoId,
                                'materia_id' => $materiaDestinoId,
                                'profesor_id' => $h->profesor_id,
                                'espacio_id' => $h->espacio_id,
                                'dia_semana' => $h->dia_semana,
                                'hora_inicio' => $h->hora_inicio ? Carbon::parse($h->hora_inicio)->format('H:i') : null,
                                'hora_fin' => $h->hora_fin ? Carbon::parse($h->hora_fin)->format('H:i') : null,
                                'es_receso' => (bool) $h->es_receso,
                                'omitir_validacion_capacidad' => true,
                            ]);
                            $clonadas++;
                        }

                        \Illuminate\Support\Facades\DB::commit();

                        \Filament\Notifications\Notification::make()
                            ->title("¡Horario clonado exitosamente!")
                            ->body("Se copiaron {$clonadas} bloque(s) a la sección {$seccionDestino->codigo}. Ahora puedes ingresar a ella y ajustar individualmente las electivas.")
                            ->success()
                            ->duration(8000)
                            ->send();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\DB::rollBack();
                        \Filament\Notifications\Notification::make()
                            ->title('Error al clonar horario')
                            ->body($e->getMessage())
                            ->danger()
                            ->duration(8000)
                            ->send();
                    }
                })
                ->visible(fn (): bool => !empty($this->seccion_id) && !empty($this->horariosAsignados)),

            Action::make('imprimirPdf')
                ->label('Vista Previa / Imprimir PDF')
                ->color('gray')
                ->icon('heroicon-o-printer')
                ->url(fn (): ?string => $this->seccion_id ? route('horarios.pdf', ['seccion_id' => $this->seccion_id]) : null)
                ->openUrlInNewTab()
                ->visible(fn (): bool => !empty($this->seccion_id)),

            Action::make('enviarRevision')
                ->label('Enviar a Revisión')
                ->color('warning')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->modalHeading('¿Enviar horario a revisión?')
                ->modalDescription('El horario de esta sección pasará a estado "En Revisión" para ser evaluado por coordinación.')
                ->action(function () {
                    if (!$this->seccion_id) return;
                    $seccion = Seccion::find($this->seccion_id);
                    if ($seccion) {
                        $seccion->update(['estado_horario' => 'revision']);
                        \Filament\Notifications\Notification::make()
                            ->title('Horario enviado a revisión')
                            ->success()
                            ->send();
                    }
                })
                ->visible(function () {
                    if (!$this->seccion_id) return false;
                    $seccion = Seccion::find($this->seccion_id);
                    return $seccion && $seccion->estado_horario === 'borrador';
                }),

            Action::make('aprobarHorario')
                ->label('Aprobar Horario (Oficial)')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->modalHeading('¿Aprobar este horario?')
                ->modalDescription('El horario se marcará como Aprobado y Oficial.')
                ->action(function () {
                    if (!$this->seccion_id) return;
                    $seccion = Seccion::find($this->seccion_id);
                    if ($seccion) {
                        $seccion->update(['estado_horario' => 'aprobado']);
                        \Filament\Notifications\Notification::make()
                            ->title('Horario aprobado exitosamente')
                            ->success()
                            ->send();
                    }
                })
                ->visible(function () {
                    if (!$this->seccion_id) return false;
                    $seccion = Seccion::find($this->seccion_id);
                    return $seccion && $seccion->estado_horario === 'revision';
                }),
                
             Action::make('volverBorrador')
                ->label('Devolver a Borrador')
                ->color('danger')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('¿Devolver a borrador?')
                ->modalDescription('Se quitará el estado actual y volverá a borrador para permitir edición.')
                ->action(function () {
                    if (!$this->seccion_id) return;
                    $seccion = Seccion::find($this->seccion_id);
                    if ($seccion) {
                        $seccion->update(['estado_horario' => 'borrador']);
                        \Filament\Notifications\Notification::make()
                            ->title('Horario devuelto a borrador')
                            ->success()
                            ->send();
                    }
                })
                ->visible(function () {
                    if (!$this->seccion_id) return false;
                    $seccion = Seccion::find($this->seccion_id);
                    return $seccion && in_array($seccion->estado_horario, ['revision', 'aprobado']);
                }),
        ];
    }
}
