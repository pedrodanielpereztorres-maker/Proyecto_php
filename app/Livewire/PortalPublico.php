<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Carrera;
use App\Models\Seccion;
use App\Models\Profesor;
use App\Models\Horario;
use App\Models\PeriodoAcademico;
use App\Models\JornadaParametro;
use Illuminate\Support\Carbon;

class PortalPublico extends Component
{
    public $modo = null; // 'estudiante' o 'docente'

    // Filtros Generales
    public $periodos = [];
    public $periodo_id = null;

    // Filtros Estudiante
    public $carreras = [];
    public $carrera_id = null;
    public $semestre = null;
    public $secciones = [];
    public $seccion_id = null;

    // Filtros Docente
    public $cedula = '';
    public $profesor_encontrado = null;

    // Datos Horario
    public $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    public $bloques = [];
    public $horariosAsignados = []; 

    public function mount()
    {
        $this->periodos = PeriodoAcademico::where('estado', 'curso')->get();
        $this->carreras = Carrera::all();
        $this->cargarBloques();
    }

    public function setModo($modo)
    {
        $this->modo = $modo;
        $this->resetearFiltros();
    }

    public function resetearFiltros()
    {
        $this->periodo_id = null;
        $this->carrera_id = null;
        $this->semestre = null;
        $this->secciones = [];
        $this->seccion_id = null;
        $this->cedula = '';
        $this->profesor_encontrado = null;
        $this->horariosAsignados = [];
    }

    public function updatedPeriodoId()
    {
        $this->cargarSecciones();
    }

    public function updatedCarreraId()
    {
        $this->cargarSecciones();
    }

    public function updatedSemestre()
    {
        $this->cargarSecciones();
    }

    public function cargarSecciones()
    {
        $this->seccion_id = null;
        $this->horariosAsignados = [];
        if ($this->periodo_id && $this->carrera_id && $this->semestre) {
            $this->secciones = Seccion::where('periodo_academico_id', $this->periodo_id)
                ->where('carrera_id', $this->carrera_id)
                ->where('semestre', $this->semestre)
                ->where('estado_horario', 'aprobado')
                ->get();
        } else {
            $this->secciones = [];
        }
    }

    public function updatedSeccionId()
    {
        $this->cargarHorarioEstudiante();
    }

    public function cargarHorarioEstudiante()
    {
        $this->horariosAsignados = [];
        if (!$this->seccion_id) return;

        $horarios = Horario::with(['materia', 'profesor', 'espacio'])
            ->where('seccion_id', $this->seccion_id)
            ->whereHas('periodoAcademico', function($q) {
                $q->where('estado', 'curso');
            })
            ->get();

        foreach ($horarios as $h) {
            if ($h->hora_inicio) {
                $key = $h->dia_semana . '_' . Carbon::parse($h->hora_inicio)->format('H:i');
                $this->horariosAsignados[$key] = $h;
            }
        }
    }

    public function buscarDocente()
    {
        $this->horariosAsignados = [];
        $this->profesor_encontrado = Profesor::where('cedula', $this->cedula)->first();

        if ($this->profesor_encontrado) {
            $horarios = Horario::with(['materia', 'seccion', 'espacio'])
                ->where('profesor_id', $this->profesor_encontrado->id)
                ->whereHas('seccion', function($q) {
                    $q->where('estado_horario', 'aprobado');
                })
                ->whereHas('periodoAcademico', function($q) {
                    $q->where('estado', 'curso');
                })
                ->get();

            foreach ($horarios as $h) {
                if ($h->hora_inicio) {
                    $key = $h->dia_semana . '_' . Carbon::parse($h->hora_inicio)->format('H:i');
                    $this->horariosAsignados[$key] = $h;
                }
            }
        } else {
            session()->flash('error_cedula', 'No se encontró un docente con esa cédula.');
        }
    }

    public function cargarBloques()
    {
        $parametro = JornadaParametro::first();
        if (!$parametro) return;
        
        $inicio = Carbon::parse($parametro->hora_inicio);
        $fin = Carbon::parse($parametro->hora_fin);
        $this->bloques = [];

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
                'es_receso' => $esReceso
            ];
        }
    }

    public function render()
    {
        $config = null;
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('configuracions')) {
                $config = \App\Models\Configuracion::first();
            }
        } catch (\Exception $e) {
        }
        
        $colorPrincipal = $config->color_principal ?? '#c71b04'; // Rojo IUTEPI por defecto
        $logo = null;
        if ($config) {
            if ($config->logo_url) {
                $logo = $config->logo_url;
            } elseif ($config->logo) {
                $logo = asset('storage/' . $config->logo);
            }
        }

        return view('livewire.portal-publico', [
            'colorPrincipal' => $colorPrincipal,
            'logo' => $logo,
            'nombreInst' => $config->nombre ?? 'IUTEPI'
        ])->layout('components.layouts.app');
    }
}
