<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario Academico - {{ $seccion->codigo }}</title>

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

        $rBase = hexdec(substr($hex, 0, 2));
        $gBase = hexdec(substr($hex, 2, 2));
        $bBase = hexdec(substr($hex, 4, 2));

        // Mezcla el color con blanco: $p = 0 devuelve el color puro, $p = 1 devuelve blanco.
        $tinte = function (float $p) use ($rBase, $gBase, $bBase): string {
            return sprintf(
                '#%02x%02x%02x',
                (int) round($rBase + (255 - $rBase) * $p),
                (int) round($gBase + (255 - $gBase) * $p),
                (int) round($bBase + (255 - $bBase) * $p)
            );
        };

        $colorSuave  = $tinte(0.92); // fondo de las celdas de clase
        $colorMedio  = $tinte(0.78); // bordes suaves y badges
        $colorFuerte = $tinte(0.20); // texto sobre fondo claro

        // Altura fija y compacta para que DomPDF no expanda las filas.
        $alturaTr = 30;
    @endphp

    <style>
        @page {
            margin: 20px 24px 72px 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            color: #334155;
            background: #ffffff;
        }

        /* ── MARCA DE AGUA ─────────────────────────────────────────────── */
        .watermark {
            position: fixed;
            top: 110px;
            left: 0;
            width: 100%;
            text-align: center;
            opacity: 0.07;
            z-index: -1;
        }
        .watermark img {
            width: 340px;
            max-height: 300px;
        }

        /* ── MEMBRETES ─────────────────────────────────────────────────── */
        .membrete-top {
            width: 100%;
            margin-bottom: 10px;
        }
        .membrete-top img {
            width: 100%;
            max-height: 80px;
        }
        .membrete-bottom {
            position: fixed;
            bottom: -58px;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .membrete-bottom img {
            width: 100%;
            max-height: 50px;
        }

        /* ── BARRA SUPERIOR ────────────────────────────────────────────── */
        .top-bar {
            height: 4px;
            background-color: {{ $color }};
            width: 100%;
            margin-bottom: 12px;
        }

        /* ── ENCABEZADO INSTITUCIONAL ──────────────────────────────────── */
        .header-container {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .header-container td {
            vertical-align: middle;
        }
        .header-logo img {
            max-height: 52px;
            max-width: 130px;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            color: {{ $color }};
            font-size: 12.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            line-height: 1.15;
        }
        .header .siglas {
            color: {{ $colorFuerte }};
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 2px;
        }
        .header h2 {
            color: #1e293b;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: 5px;
        }

        /* ── TABLA DE METADATOS ────────────────────────────────────────── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .meta-label {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .meta-value {
            color: #1e293b;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .meta-value .sub {
            font-weight: normal;
            color: #64748b;
        }

        /* ── CUADRICULA SEMANAL ────────────────────────────────────────── */
        .timetable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .timetable th {
            background-color: {{ $color }};
            color: #ffffff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 5px 3px;
            border: 1px solid {{ $color }};
            text-align: center;
        }
        .timetable td {
            border: 1px solid #e2e8f0;
        }
        .td-hora {
            width: 68px;
            background-color: #f8fafc;
            text-align: center;
            vertical-align: middle;
            padding: 2px;
        }
        .hora-start {
            color: #334155;
            font-size: 6.5pt;
            font-weight: bold;
        }
        .hora-end {
            color: #94a3b8;
            font-size: 6pt;
        }
        .td-clase {
            background-color: {{ $colorSuave }};
            border-left: 3px solid {{ $color }};
            padding: 4px 5px;
            vertical-align: top;
        }
        .clase-materia {
            color: {{ $colorFuerte }};
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 2px;
        }
        .clase-profesor {
            color: #475569;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .clase-espacio {
            color: #64748b;
            font-size: 6.5pt;
        }
        .td-receso {
            background-color: #fffbeb;
            border: 1px dashed #f59e0b;
            text-align: center;
            vertical-align: middle;
            padding: 4px;
        }
        .receso-txt {
            color: #b45309;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .td-vacio {
            background-color: #ffffff;
        }

        /* ── LEYENDA ───────────────────────────────────────────────────── */
        .legend-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            margin-bottom: 14px;
        }
        .legend-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .legend-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 7pt;
            color: #334155;
        }
        .badge {
            display: inline-block;
            background-color: {{ $colorMedio }};
            color: {{ $colorFuerte }};
            font-size: 6pt;
            font-weight: bold;
            padding: 1px 4px;
            margin-right: 4px;
        }

        /* ── FIRMAS ────────────────────────────────────────────────────── */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .firmas-table td {
            vertical-align: bottom;
            text-align: center;
            padding: 0 8px;
        }
        .firma-graficos {
            height: 46px;
            text-align: center;
        }
        .firma-graficos img.firma {
            max-height: 42px;
            max-width: 130px;
        }
        .firma-graficos img.sello {
            max-height: 44px;
            max-width: 70px;
            margin-left: 6px;
        }
        .firma-linea {
            border-top: 1px solid #475569;
            padding-top: 3px;
            color: #1e293b;
            font-size: 7pt;
            font-weight: bold;
        }
        .firma-cargo {
            color: #64748b;
            font-size: 6.5pt;
            font-weight: normal;
        }

        /* ── PIE DE PAGINA ─────────────────────────────────────────────── */
        .pie-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .pie-txt {
            color: #94a3b8;
            font-size: 6pt;
            text-align: right;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    {{-- ── MARCA DE AGUA ───────────────────────────────────────────────── --}}
    @if(! empty($watermarkBase64))
        <div class="watermark">
            <img src="{{ $watermarkBase64 }}" alt="">
        </div>
    @endif

    {{-- ── MEMBRETE DE PIE (fijo en todas las paginas) ──────────────────── --}}
    @if(! empty($membreteBottomBase64))
        <div class="membrete-bottom">
            <img src="{{ $membreteBottomBase64 }}" alt="">
        </div>
    @endif

    {{-- ── MEMBRETE DE ENCABEZADO ───────────────────────────────────────── --}}
    @if(! empty($membreteTopBase64))
        <div class="membrete-top">
            <img src="{{ $membreteTopBase64 }}" alt="">
        </div>
    @endif

    <div class="top-bar"></div>

    {{-- ── ENCABEZADO INSTITUCIONAL ─────────────────────────────────────── --}}
    <table class="header-container">
        <tr>
            <td class="header-logo" style="width: 16%; text-align: left;">
                @if(! empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td style="width: 68%;">
                <div class="header">
                    <h1>{{ $config->nombre ?? 'Instituto Universitario de Tecnologia' }}</h1>
                    @if(! empty($config->siglas))
                        <div class="siglas">{{ $config->siglas }}</div>
                    @endif
                    <h2>Horario de Clases</h2>
                </div>
            </td>
            <td style="width: 16%;"></td>
        </tr>
    </table>

    {{-- ── METADATOS ────────────────────────────────────────────────────── --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label" style="width: 11%;">PERIODO</td>
            <td class="meta-value" style="width: 44%;" colspan="3">
                {{ optional($seccion->periodoAcademico)->codigo ?? 'N/A' }}
                @if($seccion->periodoAcademico && $seccion->periodoAcademico->fecha_inicio)
                    <span class="sub">
                        (Del {{ $seccion->periodoAcademico->fecha_inicio->format('d/m/Y') }}
                        al {{ optional($seccion->periodoAcademico->fecha_fin)->format('d/m/Y') ?? 'N/A' }}
                        @if($seccion->periodoAcademico->duracion_semanas)
                            - {{ $seccion->periodoAcademico->duracion_semanas }} semanas
                        @endif)
                    </span>
                @endif
            </td>
            <td class="meta-label" style="width: 11%;">CARRERA</td>
            <td class="meta-value" style="width: 34%;" colspan="2">
                {{ optional($seccion->carrera)->nombre ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="meta-label" style="width: 11%;">SECCION</td>
            <td class="meta-value" style="width: 19%;">{{ $seccion->codigo }}</td>
            <td class="meta-label" style="width: 11%;">SEMESTRE</td>
            <td class="meta-value" style="width: 14%;">{{ $seccion->semestre ?? 'N/A' }}</td>
            <td class="meta-label" style="width: 11%;">TURNO</td>
            <td class="meta-value" style="width: 34%;">{{ optional($seccion->turno)->nombre ?? 'N/A' }}</td>
        </tr>
        @if(! empty($departamento) && ! empty($departamento->nombre))
            <tr>
                <td class="meta-label">DEPARTAMENTO</td>
                <td class="meta-value" colspan="5">{{ $departamento->nombre }}</td>
            </tr>
        @endif
    </table>

    {{-- ── CUADRICULA SEMANAL ───────────────────────────────────────────── --}}
    @php
        $skipRows = [];
        foreach ($dias as $d) {
            $skipRows[$d] = 0;
        }
        $totalBloques = count($bloques);
    @endphp

    <table class="timetable">
        <thead>
            <tr>
                <th style="width: 68px;">HORA</th>
                @foreach($dias as $dia)
                    <th>{{ $dia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($bloques as $bIndex => $bloque)
                <tr style="height: {{ $alturaTr }}px;">
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
                            $key      = $dia . '_' . $bloque['inicio'];
                            $asignado = $horariosAsignados[$key] ?? null;
                        @endphp

                        @if($asignado)
                            @php
                                // El rowspan se calcula contando cuantos bloques de la
                                // grilla caen dentro del rango de la asignacion. Las horas
                                // vienen en formato H:i, por lo que la comparacion de
                                // cadenas equivale a la comparacion cronologica.
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
                            @endphp

                            @if($asignado['es_receso'])
                                <td class="td-receso" rowspan="{{ $rowspan }}">
                                    <span class="receso-txt">RECESO</span>
                                </td>
                            @else
                                <td class="td-clase" rowspan="{{ $rowspan }}">
                                    <div class="clase-materia">{{ $asignado['materia_nombre'] }}</div>
                                    @if($asignado['profesor_apellido'] || $asignado['profesor_nombre'])
                                        <div class="clase-profesor">
                                            {{ $asignado['profesor_apellido'] }}@if($asignado['profesor_apellido'] && $asignado['profesor_nombre']), @endif{{ $asignado['profesor_nombre'] }}
                                        </div>
                                    @endif
                                    @if($asignado['espacio_codigo'])
                                        <div class="clase-espacio">Aula: {{ $asignado['espacio_codigo'] }}</div>
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

    {{-- ── LEYENDA ──────────────────────────────────────────────────────── --}}
    @php
        $leyenda = [];
        foreach ($horariosAsignados as $asig) {
            if (! empty($asig['es_receso']) || empty($asig['materia_nombre'])) {
                continue;
            }

            $mid = $asig['materia_id'] ?? $asig['materia_nombre'];

            if (! isset($leyenda[$mid])) {
                $leyenda[$mid] = [
                    'codigo'   => $asig['materia_codigo'] ?? '',
                    'materia'  => $asig['materia_nombre'],
                    'profesor' => trim(($asig['profesor_nombre'] ?? '') . ' ' . ($asig['profesor_apellido'] ?? '')),
                    'telefono' => $asig['profesor_telefono'] ?? 'N/A',
                    'aulas'    => [],
                ];
            }

            if (! empty($asig['espacio_codigo']) && ! in_array($asig['espacio_codigo'], $leyenda[$mid]['aulas'], true)) {
                $leyenda[$mid]['aulas'][] = $asig['espacio_codigo'];
            }
        }
    @endphp

    @if(count($leyenda) > 0)
        <table class="legend-table">
            <thead>
                <tr>
                    <th style="width: 40%;">ASIGNATURA</th>
                    <th style="width: 30%;">DOCENTE</th>
                    <th style="width: 14%;">AULA</th>
                    <th style="width: 16%;">TELEFONO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leyenda as $item)
                    <tr>
                        <td>
                            @if($item['codigo'])
                                <span class="badge">{{ $item['codigo'] }}</span>
                            @endif
                            {{ $item['materia'] }}
                        </td>
                        <td>{{ $item['profesor'] !== '' ? $item['profesor'] : 'N/A' }}</td>
                        <td>{{ count($item['aulas']) ? implode(', ', $item['aulas']) : 'N/A' }}</td>
                        <td>{{ $item['telefono'] !== '' ? $item['telefono'] : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ── FIRMAS ───────────────────────────────────────────────────────── --}}
    @php
        $columnasFirma = 1; // La firma del coordinador del departamento siempre se muestra.
        if (! empty($config->director_academico))   { $columnasFirma++; }
        if (! empty($config->coordinador_general))  { $columnasFirma++; }
        $anchoFirma = (int) floor(100 / $columnasFirma);

        $nombreDepartamento = (! empty($departamento) && ! empty($departamento->nombre))
            ? $departamento->nombre
            : null;

        if ($nombreCoordinador) {
            $cargoCoordinador = 'Coordinador del Departamento'
                . ($nombreDepartamento ? ' de ' . $nombreDepartamento : '');
        } elseif ($nombreDepartamento) {
            $cargoCoordinador = $nombreDepartamento;
        } else {
            $cargoCoordinador = 'Coordinacion Academica';
        }
    @endphp

    <table class="firmas-table">
        <tr>
            <td style="width: {{ $anchoFirma }}%;">
                <div class="firma-graficos">
                    @if(! empty($firmaBase64))
                        <img class="firma" src="{{ $firmaBase64 }}" alt="">
                    @endif
                    @if(! empty($selloBase64))
                        <img class="sello" src="{{ $selloBase64 }}" alt="">
                    @endif
                </div>
                <div class="firma-linea">
                    {{ $nombreCoordinador ?: 'Coordinador del Departamento' }}
                    <div class="firma-cargo">{{ $cargoCoordinador }}</div>
                </div>
            </td>

            @if(! empty($config->director_academico))
                <td style="width: {{ $anchoFirma }}%;">
                    <div class="firma-graficos"></div>
                    <div class="firma-linea">
                        {{ $config->director_academico }}
                        <div class="firma-cargo">Director Academico</div>
                    </div>
                </td>
            @endif

            @if(! empty($config->coordinador_general))
                <td style="width: {{ $anchoFirma }}%;">
                    <div class="firma-graficos"></div>
                    <div class="firma-linea">
                        {{ $config->coordinador_general }}
                        <div class="firma-cargo">Coordinador General</div>
                    </div>
                </td>
            @endif
        </tr>
    </table>

    {{-- ── PIE ──────────────────────────────────────────────────────────── --}}
    <table class="pie-table">
        <tr>
            <td style="width: 100%;">
                <div class="pie-txt">
                    Generado automaticamente el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}
                    @if(! empty($config->pie_pagina_pdf))
                        <br>{{ $config->pie_pagina_pdf }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
