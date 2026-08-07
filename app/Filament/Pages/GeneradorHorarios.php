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
    protected static ?string $navigationLabel = 'Armar Horario (Visual)';
    protected static ?string $title = 'Generador Visual de Horarios';
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

    public function mount()
    {
        $this->periodo_academico_id = PeriodoAcademico::query()->whereIn('estado', ['curso', 'planificacion'])->orderByDesc('id')->value('id');
        $this->cargarBloques();
        $this->cargarHorarios();
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
            
        $this->horariosAsignados = [];
        foreach ($horarios as $h) {
            if ($h->hora_inicio) {
                $key = $h->dia_semana . '_' . Carbon::parse($h->hora_inicio)->format('H:i');
                $this->horariosAsignados[$key] = $h;
            }
        }
        $this->renderCount++;
    }

    public function asignarBloqueAction(): Action
    {
        return Action::make('asignarBloque')
            ->label('Asignar Materia')
            ->iconButton()
            ->color('primary')
            ->size('sm')
            ->icon('heroicon-m-plus-circle')
            ->form([
                \Filament\Forms\Components\Select::make('materia_id')
                    ->label('Materia')
                    ->options(function () {
                        $query = \App\Models\Materia::query();
                        if ($this->carrera_id) {
                            $query->where('carrera_id', $this->carrera_id);
                        }
                        if ($this->semestre) {
                            $query->where('semestre', $this->semestre);
                        }
                        return $query->pluck('nombre', 'id');
                    })
                    ->required(),
                \Filament\Forms\Components\Select::make('profesor_id')
                    ->label('Docente')
                    ->options(\App\Models\Profesor::pluck('nombre', 'id'))
                    ->required(),
                \Filament\Forms\Components\Select::make('espacio_id')
                    ->label('Espacio / Aula')
                    ->options(\App\Models\Espacio::pluck('codigo', 'id'))
                    ->searchable()
                    ->required(),
                \Filament\Forms\Components\Select::make('cantidad_bloques')
                    ->label('Duración (Bloques continuos)')
                    ->options([
                        1 => '1 Bloque (40 min)',
                        2 => '2 Bloques (80 min)',
                        3 => '3 Bloques (120 min)',
                        4 => '4 Bloques (160 min)',
                    ])
                    ->default(1)
                    ->required(),
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
                        for ($j = 0; $j < $data['cantidad_bloques'] * 2; $j += 2) {
                            if (isset($this->bloques[$startIndex + $j])) {
                                $bloqueActual = $this->bloques[$startIndex + $j];
                                
                                \App\Models\Horario::create([
                                    'periodo_academico_id' => $this->periodo_academico_id,
                                    'seccion_id' => $this->seccion_id,
                                    'materia_id' => $data['materia_id'],
                                    'profesor_id' => $data['profesor_id'],
                                    'espacio_id' => $data['espacio_id'],
                                    'dia_semana' => $dia,
                                    'hora_inicio' => $bloqueActual['inicio'],
                                    'hora_fin' => \Carbon\Carbon::parse($bloqueActual['inicio'])->addMinutes(40)->format('H:i'),
                                    'es_receso' => false,
                                ]);
                            }
                        }
                        \Illuminate\Support\Facades\DB::commit();
                        $this->cargarHorarios();
                        \Filament\Notifications\Notification::make()->title('Bloques asignados correctamente')->success()->send();
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        \Illuminate\Support\Facades\DB::rollBack();
                        $errores = collect($e->errors())->flatten()->implode(', ');
                        \Filament\Notifications\Notification::make()
                            ->title('Regla de Negocio Incumplida')
                            ->body($errores)
                            ->danger()
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
            ->modalWidth('md');
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
            });
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
            });
    }
}
