<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seccion;
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
            
        $horariosAsignados = [];
        foreach ($horarios as $h) {
            if ($h->hora_inicio) {
                $horaInicioStr = Carbon::parse($h->hora_inicio)->format('H:i');
                $key = $h->dia_semana . '_' . $horaInicioStr;
                // Guardar arrays planos (igual que en la página Livewire)
                $horariosAsignados[$key] = [
                    'id'               => $h->id,
                    'materia_id'       => $h->materia_id,
                    'es_receso'        => (bool) $h->es_receso,
                    'hora_inicio'      => $horaInicioStr,
                    'hora_fin'         => Carbon::parse($h->hora_fin)->format('H:i'),
                    'materia_nombre'   => optional($h->materia)->nombre ?? '',
                    'materia_codigo'   => optional($h->materia)->codigo ?? '',
                    'profesor_nombre'  => optional($h->profesor)->nombre ?? '',
                    'profesor_apellido'=> optional($h->profesor)->apellido ?? '',
                    'profesor_telefono'=> optional($h->profesor)->telefono ?? 'N/A',
                    'espacio_codigo'   => optional($h->espacio)->codigo ?? '',
                ];
            }
        }

        // Calcular el rango activo de horas (solo bloques con clases)
        $horaMinima = null;
        $horaMaxima = null;
        foreach ($horarios as $h) {
            if ($h->hora_inicio) {
                $hi = Carbon::parse($h->hora_inicio);
                $hf = Carbon::parse($h->hora_fin);
                if (!$horaMinima || $hi->lt($horaMinima)) $horaMinima = $hi->copy();
                if (!$horaMaxima || $hf->gt($horaMaxima)) $horaMaxima = $hf->copy();
            }
        }
        
        // Generar grilla: bloques de 40 min, excepto a las 12:00 PM que es un receso de 20 min
        $parametro = JornadaParametro::first();
        $bloques = [];
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
            }
        }

        // Filtrar bloques al rango activo — usar lte para NO cortar el último bloque
        if ($horaMinima && $horaMaxima) {
            $bloques = array_values(array_filter($bloques, function ($bloque) use ($horaMinima, $horaMaxima) {
                $bloqueInicio = Carbon::parse($bloque['inicio']);
                return $bloqueInicio->gte($horaMinima) && $bloqueInicio->lt($horaMaxima);
            }));
        }
        
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
        
        $config = Configuracion::first();
        $logoBase64 = null;
        if ($config && $config->logo && file_exists(public_path('storage/' . $config->logo))) {
            $tipo = pathinfo(public_path('storage/' . $config->logo), PATHINFO_EXTENSION);
            $logoBase64 = 'data:image/' . $tipo . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $config->logo)));
        }

        // --- Configuración Global de Documentos ---
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

        // --- Firma y Sello por Departamento ---
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
        $pdf->setPaper('letter', 'landscape');
        
        // Formato: carrera_periodo_seccion.pdf
        $carreraStr = $seccion->carrera ? Str::slug($seccion->carrera->nombre) : 'carrera';
        $periodoStr = $seccion->periodoAcademico ? Str::slug($seccion->periodoAcademico->codigo) : 'periodo';
        $seccionStr = Str::slug($seccion->codigo);
        $filename = $carreraStr . '_' . $periodoStr . '_' . $seccionStr . '.pdf';
        
        return $pdf->download($filename);
    }
}
