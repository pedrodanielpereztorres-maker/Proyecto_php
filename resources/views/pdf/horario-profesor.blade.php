<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario de Clases - {{ $profesor->nombre }} {{ $profesor->apellido }}</title>

    @php
        // ── Paleta institucional derivada del color principal ──────────────────
        $color = ($config && $config->color_principal) ? $config->color_principal : '#c21807';

        $hex = ltrim($color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            $hex = 'c21807';
            $color = '#c21807';
        }

        // Paleta de materias armónica
        $paletaMaterias = [
            ['bg' => '#fef2f2', 'text' => '#991b1b', 'border' => '#fca5a5'],
            ['bg' => '#eff6ff', 'text' => '#1e40af', 'border' => '#93c5fd'],
            ['bg' => '#fefce8', 'text' => '#854d0e', 'border' => '#fde047'],
            ['bg' => '#f0fdf4', 'text' => '#166534', 'border' => '#86efac'],
            ['bg' => '#faf5ff', 'text' => '#6b21a8', 'border' => '#d8b4fe'],
            ['bg' => '#fff7ed', 'text' => '#9a3412', 'border' => '#fdba74'],
            ['bg' => '#fdf2f8', 'text' => '#9d174d', 'border' => '#f9a8d4'],
        ];

        $getColorMateria = function($nombre) use ($paletaMaterias) {
            $hash = crc32($nombre);
            return $paletaMaterias[$hash % count($paletaMaterias)];
        };

        if (!function_exists('optimizarImagenDomPDF')) {
            function optimizarImagenDomPDF($base64, $maxWidth = 800, $calidad = 85) {
                if (empty($base64)) return null;
                $datos = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
                if (!$datos) return $base64;
                $img = @imagecreatefromstring($datos);
                if (!$img) return $base64;
                
                $ancho = imagesx($img);
                $alto = imagesy($img);
                
                if ($ancho > $maxWidth) {
                    $nuevoAlto = ($alto / $ancho) * $maxWidth;
                    $nuevaImg = imagecreatetruecolor($maxWidth, $nuevoAlto);
                    imagealphablending($nuevaImg, false);
                    imagesavealpha($nuevaImg, true);
                    imagecopyresampled($nuevaImg, $img, 0, 0, 0, 0, $maxWidth, $nuevoAlto, $ancho, $alto);
                    imagedestroy($img);
                    $img = $nuevaImg;
                }
                
                ob_start();
                imagejpeg($img, null, $calidad);
                $datosOptimizados = ob_get_clean();
                imagedestroy($img);
                
                return 'data:image/jpeg;base64,' . base64_encode($datosOptimizados);
            }
        }
        
        $watermarkBase64 = optimizarImagenDomPDF($watermarkBase64 ?? null);
        $logoBase64 = optimizarImagenDomPDF($logoBase64 ?? null, 400);
        $firmaBase64 = optimizarImagenDomPDF($firmaBase64 ?? null, 300);
        $selloBase64 = optimizarImagenDomPDF($selloBase64 ?? null, 200);
    @endphp

    <style>
        @page {
            size: letter portrait;
            margin: 25pt 30pt 25pt 30pt;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 8pt;
            padding: 0 15pt;
        }

        /* ── UTILIDADES ─────────────────────────────────────────────── */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .text-xs { font-size: 6.5pt; }
        .text-sm { font-size: 7.5pt; }

        /* ── MARCA DE AGUA ─────────────────────────────────────────── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            text-align: center;
            opacity: 0.04;
            z-index: -1;
        }
        .watermark img {
            width: 100%;
            height: auto;
        }

        /* ── HEADER INSTITUCIONAL CENTRADO ─────────────────────────── */
        .header-container {
            text-align: center;
            margin-bottom: 8px;
        }
        .header-logo {
            margin-bottom: 4px;
            text-align: center;
        }
        .header-logo img {
            max-height: 52px;
            max-width: 280px;
            height: auto;
            display: inline-block;
        }
        .institution-siglas {
            font-size: 17pt;
            font-weight: bold;
            color: {{ $color }};
            line-height: 1.1;
            letter-spacing: 1px;
        }
        .institution-nombre {
            font-size: 7.5pt;
            color: #475569;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .horario-titulo {
            font-size: 12.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 2px;
            margin-bottom: 8px;
        }

        /* ── TEXTO DE COMPROMISO DEL DOCENTE ───────────────────────── */
        .compromiso-container {
            width: 92%;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 12px;
            text-align: center;
        }
        .compromiso-texto {
            font-size: 8pt;
            line-height: 1.45;
            color: #334155;
            text-align: justify;
        }
        .compromiso-rango {
            text-align: center;
            font-size: 8.5pt;
            font-weight: bold;
            color: {{ $color }};
            margin-top: 5px;
            letter-spacing: 0.5px;
        }

        /* ── CUADRICULA SEMANAL ─────────────────────────────────────── */
        .timetable-wrapper {
            width: 92%;
            margin-left: auto;
            margin-right: auto;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            margin-bottom: 14px;
            overflow: hidden;
        }
        .timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .timetable th {
            background-color: {{ $color }};
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 2px;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }
        .timetable th:last-child { border-right: none; }
        
        .timetable td {
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            vertical-align: middle;
            text-align: center;
            padding: 2px;
        }
        .timetable td:last-child { border-right: none; }
        .timetable tr:last-child td { border-bottom: none; }

        .td-hora {
            width: 60px;
            background-color: #f8fafc;
            text-align: center;
            vertical-align: middle;
            padding: 3px 1px !important;
        }
        .hora-start {
            color: #0f172a;
            font-size: 6.5pt;
            font-weight: bold;
        }
        .hora-end {
            color: #94a3b8;
            font-size: 5.5pt;
            margin-top: 1px;
        }
        
        .td-vacio {
            background-color: #ffffff;
        }

        /* ── TARJETAS DE MATERIA ────────────────────────────────────── */
        .materia-card {
            border-radius: 4px;
            padding: 3px;
            text-align: center;
        }
        .materia-nombre {
            font-size: 7pt;
            font-weight: bold;
            line-height: 1.2;
            word-wrap: break-word;
            text-align: center;
        }
        .materia-profesor {
            color: #334155;
            font-size: 5.8pt;
            margin-bottom: 1px;
            line-height: 1.1;
            text-align: center;
        }
        .materia-aula {
            color: #64748b;
            font-size: 5.8pt;
            font-weight: bold;
            text-align: center;
        }

        /* ── WIDGETS / TABLAS RESUMEN ──────────────────────────────── */
        .widget-container {
            width: 92%;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .widget-title {
            background-color: #f8fafc;
            color: #475569;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        .prof-table {
            width: 100%;
            border-collapse: collapse;
        }
        .prof-table th {
            background-color: #ffffff;
            color: #64748b;
            font-size: 6.2pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3.5px 6px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .prof-table td {
            padding: 3px 6px;
            font-size: 6.5pt;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .prof-table tr:last-child td {
            border-bottom: none;
        }
        .prof-indicator {
            display: inline-block;
            width: 3px;
            height: 9px;
            margin-right: 3px;
            border-radius: 2px;
            vertical-align: middle;
        }

        /* ── LINEA DE DATOS DEL DOCENTE ────────────────────────────── */
        .docente-info-table {
            width: 92%;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 12px;
            border-collapse: collapse;
            font-size: 8pt;
            color: #1e293b;
        }
        .docente-info-table td {
            padding: 3px 0;
        }

        /* ── FIRMAS ─────────────────────────────────────────────────── */
        .firmas-table {
            width: 85%;
            margin-left: auto;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .firmas-table td {
            vertical-align: bottom;
            text-align: center;
            padding: 0 15px;
        }
        .firma-graficos {
            height: 55px;
            position: relative;
            margin-bottom: 2px;
        }
        .firma-graficos img.firma {
            max-height: 50px;
            max-width: 140px;
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }
        .firma-graficos img.sello {
            max-height: 55px;
            max-width: 55px;
            position: absolute;
            bottom: -5px;
            right: 15px;
            opacity: 0.85;
            z-index: 2;
        }
        .firma-linea {
            border-top: 1.5px solid #334155;
            padding-top: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
        }
        .firma-cargo {
            font-size: 6.5pt;
            color: #64748b;
            font-weight: normal;
            margin-top: 1px;
            text-transform: none;
        }

        /* ── BARRA INFERIOR DECORATIVA ──────────────────────────────── */
        .footer-bar {
            position: fixed;
            bottom: -25pt;
            left: -45pt;
            right: -45pt;
            height: 24px;
            background-color: {{ $color }};
            color: #ffffff;
            text-align: center;
            line-height: 24px;
            font-size: 7.5pt;
            font-weight: bold;
            font-style: italic;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    {{-- MARCA DE AGUA --}}
    @if(! empty($watermarkBase64))
        <div class="watermark">
            <img src="{{ $watermarkBase64 }}" alt="">
        </div>
    @endif

    {{-- HEADER INSTITUCIONAL CENTRADO --}}
    <div class="header-container">
        @if(! empty($logoBase64))
            <div class="header-logo">
                <img src="{{ $logoBase64 }}" alt="Logo">
            </div>
        @else
            <div class="institution-siglas">
                {{ $config->siglas ?? 'IUTEPI' }}
            </div>
            <div class="institution-nombre">
                {{ $config->nombre ?? 'Instituto Universitario de Tecnología para la Informática' }}
            </div>
        @endif
        <div class="horario-titulo">
            HORARIO DE CLASES
        </div>
    </div>

    {{-- TEXTO OFICIAL DE COMPROMISO DEL DOCENTE --}}
    <div class="compromiso-container">
        <p class="compromiso-texto">
            Yo, <strong>{{ $profesor->nombre }} {{ $profesor->apellido }}</strong>, por medio de la presente hago constar que he recibido el horario de clases que me fue asignado por el Instituto Universitario de Tecnología para la Informática <strong>IUTEPI</strong>, extensión Acarigua, correspondiente para semestre: <strong>{{ optional($periodo)->codigo ?? 'N/A' }}</strong>. El cual me comprometo a cumplir a cabalidad tanto en la hora de entrada como la de salida y a no modificarlo bajo ningún concepto sin previa autorización.
        </p>
        @if($periodo && $periodo->fecha_inicio)
            <div class="compromiso-rango">
                Del (<strong>{{ $periodo->fecha_inicio->format('d/m/Y') }}</strong>) al (<strong>{{ optional($periodo->fecha_fin)->format('d/m/Y') ?? 'N/A' }}</strong>)
            </div>
        @endif
    </div>

    {{-- CUADRICULA SEMANAL --}}
    @php
        $skipRows = [];
        foreach ($dias as $d) {
            $skipRows[$d] = 0;
        }
        $totalBloques = count($bloques);
    @endphp

    <div class="timetable-wrapper no-break">
        <table class="timetable">
            <thead>
                <tr>
                    <th style="width: 60px;">HORA</th>
                    @foreach($dias as $dia)
                        <th>{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($bloques as $bIndex => $bloque)
                    <tr style="height: 26px;">
                        <td class="td-hora">
                            <div class="hora-start">{{ $bloque['inicio_ampm'] }}</div>
                            <div class="hora-end">{{ $bloque['fin_ampm'] }}</div>
                        </td>

                        @foreach($dias as $dia)
                            @if($skipRows[$dia] > 0)
                                @php $skipRows[$dia]--; @endphp
                                @continue
                            @endif

                            @php
                                $key = $dia . '_' . $bloque['inicio'];
                                $asignado = $horariosAsignados[$key] ?? null;
                            @endphp

                            @if($asignado)
                                @php
                                    $rowspan = 0;
                                    for ($k = $bIndex; $k < $totalBloques; $k++) {
                                        if ($bloques[$k]['inicio'] < $asignado['hora_fin']) {
                                            $rowspan++;
                                        } else {
                                            break;
                                        }
                                    }
                                    $rowspan = max(1, $rowspan);
                                    
                                    if ($rowspan > 1) {
                                        $skipRows[$dia] = $rowspan - 1;
                                    }
                                    
                                    $horaIniFmt = \Carbon\Carbon::parse($asignado['hora_inicio'])->format('h:i A');
                                    $horaFinFmt = \Carbon\Carbon::parse($asignado['hora_fin'])->format('h:i A');
                                @endphp

                                @if($asignado['es_receso'])
                                    <td class="td-receso" rowspan="{{ $rowspan }}" style="background-color: #fef3c7; border-left: 3px solid #f59e0b; text-align: center; vertical-align: middle; padding: 4px 2px;">
                                        <div style="color: #b45309; font-size: 7pt; font-weight: bold;">RECESO</div>
                                        <div style="font-size: 5.5pt; color: #b45309; font-weight: bold; margin-top: 2px;">
                                            {{ $horaIniFmt }} &ndash; {{ $horaFinFmt }}
                                        </div>
                                    </td>
                                @else
                                    @php $col = $getColorMateria($asignado['materia_nombre']); @endphp
                                    <td class="td-clase" rowspan="{{ $rowspan }}" style="background-color: {{ $col['bg'] }}; border-left: 3.5px solid {{ $col['border'] }}; padding: 4px 3px; vertical-align: middle; text-align: center;">
                                        <div class="materia-nombre" style="color: {{ $col['text'] }}; font-weight: bold; font-size: 7pt; line-height: 1.2; word-wrap: break-word; text-align: center;">
                                            {{ $asignado['materia_nombre'] }}
                                        </div>
                                        <div style="font-size: 6pt; font-weight: bold; color: {{ $col['text'] }}; margin-top: 2px; margin-bottom: 2px; text-align: center;">
                                            {{ $horaIniFmt }} &ndash; {{ $horaFinFmt }}
                                        </div>
                                        <div class="materia-profesor" style="color: #334155; font-size: 5.8pt; margin-bottom: 1px; line-height: 1.1; text-align: center;">
                                            Sec: <strong>{{ $asignado['seccion_codigo'] }}</strong> ({{ $asignado['semestre'] }}° Sem)
                                        </div>
                                        @if($asignado['espacio_codigo'])
                                            <div class="materia-aula" style="color: #64748b; font-size: 5.8pt; font-weight: bold; text-align: center;">
                                                Aula: {{ $asignado['espacio_codigo'] }}
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            @else
                                <td class="td-vacio"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- RESUMEN COMPACTO DE CARGA ACADEMICA --}}
    @php
        $resumenCarga = [];
        $totalHorasSemanales = 0;
        foreach ($horariosAsignados as $asig) {
            if (!empty($asig['es_receso']) || empty($asig['materia_nombre'])) continue;
            
            $uid = ($asig['materia_id'] ?? $asig['materia_nombre']) . '_' . ($asig['seccion_codigo'] ?? '');
            $horaInicio = \Carbon\Carbon::parse($asig['hora_inicio']);
            $horaFin = \Carbon\Carbon::parse($asig['hora_fin']);
            $duracion = $horaInicio->diffInMinutes($horaFin) / 60;
            
            if (!isset($resumenCarga[$uid])) {
                $resumenCarga[$uid] = [
                    'materia' => $asig['materia_nombre'],
                    'carrera' => $asig['carrera_nombre'] ?? 'N/A',
                    'seccion' => $asig['seccion_codigo'] ?: 'N/A',
                    'semestre' => $asig['semestre'] ? $asig['semestre'] . '° Sem' : 'N/A',
                    'aulas' => [],
                    'horas_semana' => 0,
                ];
            }
            if (!empty($asig['espacio_codigo']) && !in_array($asig['espacio_codigo'], $resumenCarga[$uid]['aulas'], true)) {
                $resumenCarga[$uid]['aulas'][] = $asig['espacio_codigo'];
            }
            $resumenCarga[$uid]['horas_semana'] += $duracion;
            $totalHorasSemanales += $duracion;
        }
    @endphp

    @if(count($resumenCarga) > 0)
        <div class="widget-container no-break">
            <div class="widget-title">RESUMEN DE CARGA ACADÉMICA</div>
            <table class="prof-table">
                <thead>
                    <tr>
                        <th style="width: 34%;">Materia</th>
                        <th style="width: 24%;">Carrera</th>
                        <th style="width: 12%;">Semestre</th>
                        <th style="width: 10%;">Sección</th>
                        <th style="width: 10%;">Aula</th>
                        <th style="width: 10%; text-align: right;">Horas/Sem</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resumenCarga as $item)
                        @php $c = $getColorMateria($item['materia']); @endphp
                        <tr>
                            <td class="text-bold" style="color: #1e293b;">
                                <span class="prof-indicator" style="background-color: {{ $c['border'] }};"></span>
                                {{ $item['materia'] }}
                            </td>
                            <td class="text-muted">{{ $item['carrera'] }}</td>
                            <td class="text-muted">{{ $item['semestre'] }}</td>
                            <td class="text-muted text-bold">{{ $item['seccion'] }}</td>
                            <td class="text-muted text-bold">{{ count($item['aulas']) ? implode(', ', $item['aulas']) : 'N/A' }}</td>
                            <td class="text-bold" style="text-align: right; color: {{ $color }};">{{ round($item['horas_semana'], 1) }}h</td>
                        </tr>
                    @endforeach
                    <tr style="background-color: #f8fafc;">
                        <td colspan="5" class="text-bold" style="text-align: right; padding-top: 3px; font-size: 6.5pt;">TOTAL HORAS ACADÉMICAS:</td>
                        <td class="text-bold" style="text-align: right; padding-top: 3px; font-size: 7pt; color: {{ $color }};">{{ round($totalHorasSemanales, 1) }}h</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    {{-- LINEA DE DATOS DEL DOCENTE --}}
    <table class="docente-info-table no-break">
        <tr>
            <td style="width: 45%;"><strong>Docente:</strong> {{ $profesor->nombre }} {{ $profesor->apellido }}</td>
            <td style="width: 30%;"><strong>Cédula:</strong> {{ $profesor->cedula ?: '_________________' }}</td>
            <td style="width: 25%; text-align: right;"><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    {{-- FIRMAS OFICIALES --}}
    @php
        $nombreDepartamento = (!empty($departamento) && !empty($departamento->nombre)) ? $departamento->nombre : null;
        if ($nombreCoordinador) {
            $cargoCoordinador = 'Coordinador del Departamento' . ($nombreDepartamento ? ' de ' . $nombreDepartamento : '');
        } elseif ($nombreDepartamento) {
            $cargoCoordinador = $nombreDepartamento;
        } else {
            $cargoCoordinador = 'Coordinación Académica';
        }
    @endphp

    <table class="firmas-table no-break">
        <tr>
            <td style="width: 50%;">
                <div class="firma-graficos">
                    @if(!empty($firmaBase64))
                        <img class="firma" src="{{ $firmaBase64 }}" alt="">
                    @endif
                    @if(!empty($selloBase64))
                        <img class="sello" src="{{ $selloBase64 }}" alt="">
                    @endif
                </div>
                <div class="firma-linea">
                    {{ $nombreCoordinador ?: 'COORDINADOR DEL DEPARTAMENTO' }}
                    @if(!empty($departamento) && !empty($departamento->cedula_coordinador))
                        <div style="font-size: 6.5pt; color: #475569; font-weight: normal; margin-top: 0.5px;">C.I. {{ $departamento->cedula_coordinador }}</div>
                    @endif
                    <div class="firma-cargo">{{ $cargoCoordinador }}</div>
                </div>
            </td>

            <td style="width: 50%;">
                <div class="firma-graficos"></div>
                <div class="firma-linea">
                    {{ $profesor->nombre }} {{ $profesor->apellido }}
                    @if($profesor->cedula)
                        <div style="font-size: 6.5pt; color: #475569; font-weight: normal; margin-top: 0.5px;">C.I. {{ $profesor->cedula }}</div>
                    @endif
                    <div class="firma-cargo">Firma del Docente (Conforme)</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- BARRA INFERIOR INSTITUCIONAL --}}
    <div class="footer-bar">
        Formación tecnológica para un mejor futuro
    </div>

</body>
</html>
