<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seccion;
use App\Models\Horario;
use App\Models\JornadaParametro;
use App\Models\Configuracion;
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
                $key = $h->dia_semana . '_' . Carbon::parse($h->hora_inicio)->format('H:i');
                $horariosAsignados[$key] = $h;
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
            $fin = Carbon::parse($parametro->hora_fin);
            while ($inicio->lt($fin)) {
                $esReceso = ($inicio->format('H:i') === '12:00');
                $duracion = $esReceso ? 20 : 40;

                $inicioStr = $inicio->format('H:i');
                $inicioAmpm = $inicio->format('h:i A');
                $inicio->addMinutes($duracion);

                $bloques[] = [
                    'inicio' => $inicioStr,
                    'inicio_ampm' => $inicioAmpm,
                    'fin_ampm' => $inicio->format('h:i A')
                ];
            }
        }

        // Filtrar bloques al rango activo (donde hay clases)
        if ($horaMinima && $horaMaxima) {
            $bloques = array_values(array_filter($bloques, function($bloque) use ($horaMinima, $horaMaxima) {
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

        $pdf = Pdf::loadView('pdf.horario', compact('seccion', 'horariosAsignados', 'bloques', 'dias', 'config', 'logoBase64'));
        $pdf->setPaper('letter', 'portrait');
        
        // Formato: carrera_periodo_seccion.pdf
        $carreraStr = $seccion->carrera ? Str::slug($seccion->carrera->nombre) : 'carrera';
        $periodoStr = $seccion->periodoAcademico ? Str::slug($seccion->periodoAcademico->codigo) : 'periodo';
        $seccionStr = Str::slug($seccion->codigo);
        $filename = $carreraStr . '_' . $periodoStr . '_' . $seccionStr . '.pdf';
        
        return $pdf->download($filename);
    }
}
