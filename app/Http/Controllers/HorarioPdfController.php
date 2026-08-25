<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seccion;
use App\Models\Profesor;
use App\Models\PeriodoAcademico;
use App\Models\Departamento;
use App\Models\Horario;
use App\Models\JornadaParametro;
use App\Models\Configuracion;
use App\Models\ConfiguracionDocumento;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class HorarioPdfController extends Controller
{
    public function descargar($seccion_id)
    {
        $seccion = Seccion::with(['carrera', 'periodoAcademico', 'turno'])->findOrFail($seccion_id);
        
        $horarios = Horario::with(['materia', 'profesor', 'espacio'])
            ->where('seccion_id', $seccion_id)
            ->get();
            
        $parametro = JornadaParametro::first();
        $bloques = [];
        $finesBloques = [];
        if ($parametro) {
            $inicio = Carbon::parse($parametro->hora_inicio);
            $fin    = Carbon::parse($parametro->hora_fin);
            while ($inicio->lt($fin)) {
                $esReceso = ($inicio->format('H:i') === '12:00');
                $duracion = $esReceso ? 20 : 40;

                $inicioStr  = $inicio->format('H:i');
                $inicioAmpm = $inicio->format('h:i A');
                $inicio->addMinutes($duracion);

                $bloques[] = [
                    'inicio'            => $inicioStr,
                    'inicio_ampm'       => $inicioAmpm,
                    'fin_ampm'          => $inicio->format('h:i A'),
                    'es_receso_default' => $esReceso,
                ];
                $finesBloques[] = $inicio->format('H:i');
            }
        }

        $horariosAsignados = [];
        $horaMinima = null;
        $horaMaxima = null;

        foreach ($horarios as $h) {
            if (! $h->hora_inicio) {
                continue;
            }

            $horaInicioStr = Carbon::parse($h->hora_inicio)->format('H:i');
            $horaFinStr    = Carbon::parse($h->hora_fin)->format('H:i');

            $inicioBloque = $horaInicioStr;
            for ($i = count($bloques) - 1; $i >= 0; $i--) {
                if ($bloques[$i]['inicio'] <= $horaInicioStr) {
                    if ($finesBloques[$i] > $horaInicioStr) {
                        $inicioBloque = $bloques[$i]['inicio'];
                    }
                    break;
                }
            }

            $key = $h->dia_semana . '_' . $inicioBloque;

            if (isset($horariosAsignados[$key])) {
                continue;
            }

            $horariosAsignados[$key] = [
                'id'               => $h->id,
                'materia_id'       => $h->materia_id,
                'es_receso'        => (bool) $h->es_receso,
                'hora_inicio'      => $horaInicioStr,
                'hora_fin'         => $horaFinStr,
                'materia_nombre'   => optional($h->materia)->nombre ?? '',
                'materia_codigo'   => optional($h->materia)->codigo ?? '',
                'profesor_nombre'  => optional($h->profesor)->nombre ?? '',
                'profesor_apellido'=> optional($h->profesor)->apellido ?? '',
                'profesor_telefono'=> optional($h->profesor)->telefono ?? 'N/A',
                'espacio_codigo'   => optional($h->espacio)->codigo ?? '',
            ];

            $hi = Carbon::parse($inicioBloque);
            $hf = Carbon::parse($h->hora_fin);
            if (! $horaMinima || $hi->lt($horaMinima)) {
                $horaMinima = $hi->copy();
            }
            if (! $horaMaxima || $hf->gt($horaMaxima)) {
                $horaMaxima = $hf->copy();
            }
        }

        if ($horaMinima && $horaMaxima) {
            $bloques = array_values(array_filter($bloques, function ($bloque) use ($horaMinima, $horaMaxima) {
                $bloqueInicio = Carbon::parse($bloque['inicio']);
                return $bloqueInicio->gte($horaMinima) && $bloqueInicio->lt($horaMaxima);
            }));
        }

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $config = Configuracion::first();
        $logoBase64 = null;
        if ($config) {
            if (!empty($config->logo_url)) {
                try {
                    $imgData = @file_get_contents($config->logo_url);
                    if ($imgData) {
                        $tipo = pathinfo(parse_url($config->logo_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                        $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($imgData);
                    }
                } catch (\Throwable $e) {}
            }
            if (!$logoBase64 && !empty($config->logo)) {
                $posiblesRutas = [
                    public_path('storage/' . $config->logo),
                    storage_path('app/public/' . $config->logo),
                    public_path($config->logo),
                ];
                foreach ($posiblesRutas as $ruta) {
                    if (file_exists($ruta) && is_file($ruta)) {
                        $tipo = pathinfo($ruta, PATHINFO_EXTENSION) ?: 'png';
                        $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents($ruta));
                        break;
                    }
                }
            }
        }

        $configDoc = ConfiguracionDocumento::first();
        $membreteTopBase64 = null;
        $membreteBottomBase64 = null;
        $watermarkBase64 = null;

        if ($configDoc) {
            if ($configDoc->membrete_encabezado && file_exists(public_path('storage/' . $configDoc->membrete_encabezado))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->membrete_encabezado), PATHINFO_EXTENSION);
                $membreteTopBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->membrete_encabezado)));
            }
            if ($configDoc->membrete_pie && file_exists(public_path('storage/' . $configDoc->membrete_pie))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->membrete_pie), PATHINFO_EXTENSION);
                $membreteBottomBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->membrete_pie)));
            }
            if ($configDoc->marca_de_agua && file_exists(public_path('storage/' . $configDoc->marca_de_agua))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->marca_de_agua), PATHINFO_EXTENSION);
                $watermarkBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->marca_de_agua)));
            }
        }

        $departamento = $seccion->carrera ? $seccion->carrera->departamento : null;
        $nombreCoordinador = $departamento ? $departamento->nombre_coordinador : null;
        $firmaBase64 = null;
        $selloBase64 = null;

        if ($departamento) {
            if ($departamento->firma_coordinador && file_exists(public_path('storage/' . $departamento->firma_coordinador))) {
                $tipo = pathinfo(public_path('storage/' . $departamento->firma_coordinador), PATHINFO_EXTENSION);
                $firmaBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $departamento->firma_coordinador)));
            }
            if ($departamento->sello_departamento && file_exists(public_path('storage/' . $departamento->sello_departamento))) {
                $tipo = pathinfo(public_path('storage/' . $departamento->sello_departamento), PATHINFO_EXTENSION);
                $selloBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $departamento->sello_departamento)));
            }
        }

        $pdf = Pdf::loadView('pdf.horario', compact('seccion', 'horariosAsignados', 'bloques', 'dias', 'config', 'logoBase64', 'configDoc', 'membreteTopBase64', 'membreteBottomBase64', 'watermarkBase64', 'nombreCoordinador', 'firmaBase64', 'selloBase64', 'departamento'));
        $pdf->setPaper('letter', 'portrait');
        
        $carreraStr = $seccion->carrera ? Str::slug($seccion->carrera->nombre) : 'carrera';
        $periodoStr = $seccion->periodoAcademico ? Str::slug($seccion->periodoAcademico->codigo) : 'periodo';
        $seccionStr = Str::slug($seccion->codigo);
        $filename = $carreraStr . '_' . $periodoStr . '_' . $seccionStr . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function descargarProfesor(Request $request, $profesor_id)
    {
        $profesor = Profesor::with(['nivelAcademico', 'especialidad'])->findOrFail($profesor_id);
        
        $periodo_academico_id = $request->query('periodo_academico_id');
        $periodo = null;
        if ($periodo_academico_id) {
            $periodo = PeriodoAcademico::find($periodo_academico_id);
        }
        if (!$periodo) {
            $periodo = PeriodoAcademico::whereIn('estado', ['curso', 'planificacion'])->orderByDesc('id')->first();
        }

        $horarios = Horario::with(['materia.carrera.departamento', 'espacio', 'seccion.turno', 'periodoAcademico'])
            ->where('profesor_id', $profesor_id)
            ->when($periodo, fn ($q) => $q->where('periodo_academico_id', $periodo->id))
            ->get();

        if ($horarios->isEmpty()) {
            return response('<div style="font-family: Arial, sans-serif; text-align: center; padding: 60px 20px; color: #334155; max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <div style="font-size: 52px; margin-bottom: 12px;">📅</div>
                <h2 style="color: #e11d48; margin-bottom: 12px; font-size: 22px;">Docente sin Carga Horaria Asignada</h2>
                <p style="font-size: 15px; color: #64748b; line-height: 1.5; margin-bottom: 24px;">El profesor <strong>' . htmlspecialchars($profesor->nombre . ' ' . $profesor->apellido) . '</strong> no tiene ninguna clase asignada en el período <strong>' . htmlspecialchars(optional($periodo)->codigo ?? 'actual') . '</strong>.</p>
                <a href="javascript:window.close()" style="display: inline-block; padding: 10px 24px; background: #e11d48; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 14px;">Cerrar Pestaña</a>
            </div>', 404);
        }

        $parametro = JornadaParametro::first();
        $bloques = [];
        $finesBloques = [];
        if ($parametro) {
            $inicio = Carbon::parse($parametro->hora_inicio);
            $fin    = Carbon::parse($parametro->hora_fin);
            while ($inicio->lt($fin)) {
                $esReceso = ($inicio->format('H:i') === '12:00');
                $duracion = $esReceso ? 20 : 40;

                $inicioStr  = $inicio->format('H:i');
                $inicioAmpm = $inicio->format('h:i A');
                $inicio->addMinutes($duracion);

                $bloques[] = [
                    'inicio'            => $inicioStr,
                    'inicio_ampm'       => $inicioAmpm,
                    'fin_ampm'          => $inicio->format('h:i A'),
                    'es_receso_default' => $esReceso,
                ];
                $finesBloques[] = $inicio->format('H:i');
            }
        }

        $horariosAsignados = [];
        $horaMinima = null;
        $horaMaxima = null;

        foreach ($horarios as $h) {
            if (! $h->hora_inicio) {
                continue;
            }

            $horaInicioStr = Carbon::parse($h->hora_inicio)->format('H:i');
            $horaFinStr    = Carbon::parse($h->hora_fin)->format('H:i');

            $inicioBloque = $horaInicioStr;
            for ($i = count($bloques) - 1; $i >= 0; $i--) {
                if ($bloques[$i]['inicio'] <= $horaInicioStr) {
                    if ($finesBloques[$i] > $horaInicioStr) {
                        $inicioBloque = $bloques[$i]['inicio'];
                    }
                    break;
                }
            }

            $key = $h->dia_semana . '_' . $inicioBloque;

            if (isset($horariosAsignados[$key])) {
                continue;
            }

            $horariosAsignados[$key] = [
                'id'               => $h->id,
                'materia_id'       => $h->materia_id,
                'es_receso'        => (bool) $h->es_receso,
                'hora_inicio'      => $horaInicioStr,
                'hora_fin'         => $horaFinStr,
                'materia_nombre'   => optional($h->materia)->nombre ?? '',
                'materia_codigo'   => optional($h->materia)->codigo ?? '',
                'carrera_nombre'   => optional(optional($h->materia)->carrera)->nombre ?? '',
                'seccion_codigo'   => optional($h->seccion)->codigo ?? '',
                'semestre'         => optional($h->seccion)->semestre ?? optional($h->materia)->semestre ?? '',
                'espacio_codigo'   => optional($h->espacio)->codigo ?? '',
            ];

            $hi = Carbon::parse($inicioBloque);
            $hf = Carbon::parse($h->hora_fin);
            if (! $horaMinima || $hi->lt($horaMinima)) {
                $horaMinima = $hi->copy();
            }
            if (! $horaMaxima || $hf->gt($horaMaxima)) {
                $horaMaxima = $hf->copy();
            }
        }

        if ($horaMinima && $horaMaxima) {
            $bloques = array_values(array_filter($bloques, function ($bloque) use ($horaMinima, $horaMaxima) {
                $bloqueInicio = Carbon::parse($bloque['inicio']);
                return $bloqueInicio->gte($horaMinima) && $bloqueInicio->lt($horaMaxima);
            }));
        }

        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

        $config = Configuracion::first();
        $logoBase64 = null;
        if ($config) {
            if (!empty($config->logo_url)) {
                try {
                    $imgData = @file_get_contents($config->logo_url);
                    if ($imgData) {
                        $tipo = pathinfo(parse_url($config->logo_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                        $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode($imgData);
                    }
                } catch (\Throwable $e) {}
            }
            if (!$logoBase64 && !empty($config->logo)) {
                $posiblesRutas = [
                    public_path('storage/' . $config->logo),
                    storage_path('app/public/' . $config->logo),
                    public_path($config->logo),
                ];
                foreach ($posiblesRutas as $ruta) {
                    if (file_exists($ruta) && is_file($ruta)) {
                        $tipo = pathinfo($ruta, PATHINFO_EXTENSION) ?: 'png';
                        $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents($ruta));
                        break;
                    }
                }
            }
        }

        $configDoc = ConfiguracionDocumento::first();
        $membreteTopBase64 = null;
        $membreteBottomBase64 = null;
        $watermarkBase64 = null;

        if ($configDoc) {
            if ($configDoc->membrete_encabezado && file_exists(public_path('storage/' . $configDoc->membrete_encabezado))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->membrete_encabezado), PATHINFO_EXTENSION);
                $membreteTopBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->membrete_encabezado)));
            }
            if ($configDoc->membrete_pie && file_exists(public_path('storage/' . $configDoc->membrete_pie))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->membrete_pie), PATHINFO_EXTENSION);
                $membreteBottomBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->membrete_pie)));
            }
            if ($configDoc->marca_de_agua && file_exists(public_path('storage/' . $configDoc->marca_de_agua))) {
                $tipo = pathinfo(public_path('storage/' . $configDoc->marca_de_agua), PATHINFO_EXTENSION);
                $watermarkBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $configDoc->marca_de_agua)));
            }
        }

        $departamento = null;
        foreach ($horarios as $h) {
            if ($h->materia && $h->materia->carrera && $h->materia->carrera->departamento) {
                $departamento = $h->materia->carrera->departamento;
                break;
            }
        }
        if (!$departamento) {
            $departamento = Departamento::first();
        }

        $nombreCoordinador = $departamento ? $departamento->nombre_coordinador : null;
        $firmaBase64 = null;
        $selloBase64 = null;

        if ($departamento) {
            if ($departamento->firma_coordinador && file_exists(public_path('storage/' . $departamento->firma_coordinador))) {
                $tipo = pathinfo(public_path('storage/' . $departamento->firma_coordinador), PATHINFO_EXTENSION);
                $firmaBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $departamento->firma_coordinador)));
            }
            if ($departamento->sello_departamento && file_exists(public_path('storage/' . $departamento->sello_departamento))) {
                $tipo = pathinfo(public_path('storage/' . $departamento->sello_departamento), PATHINFO_EXTENSION);
                $selloBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $departamento->sello_departamento)));
            }
        }

        $pdf = Pdf::loadView('pdf.horario-profesor', compact('profesor', 'periodo', 'horariosAsignados', 'bloques', 'dias', 'config', 'logoBase64', 'configDoc', 'membreteTopBase64', 'membreteBottomBase64', 'watermarkBase64', 'nombreCoordinador', 'firmaBase64', 'selloBase64', 'departamento'));
        $pdf->setPaper('letter', 'portrait');
        
        $nombreProfStr = Str::slug($profesor->apellido . '_' . $profesor->nombre);
        $periodoStr = $periodo ? Str::slug($periodo->codigo) : 'periodo';
        $filename = 'horario_docente_' . $nombreProfStr . '_' . $periodoStr . '.pdf';
        
        return $pdf->stream($filename);
    }
}
