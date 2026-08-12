<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario Académico - {{ $seccion->codigo }}</title>
    @php
        $color = ($config && $config->color_principal) ? $config->color_principal : '#c21807'; // Rojo IUTEPI
        $colorBg = $color . '15'; // Fondo claro
        
        // Altura fija y compacta para evitar que DomPDF expanda las celdas
        $alturaTr = 32;
    @endphp
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 7.5pt;
            color: #334155;
            background: #fff;
            margin: 20px 25px; /* Márgenes estándar para impresión */
        }

        /* ── TOP BAR ── */
        .top-bar {
            height: 4px;
            background-color: {{ $color }};
            width: 100%;
            margin-bottom: 15px;
        }

        /* ── HEADER ── */
        .header-container {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .header-container td {
            vertical-align: middle;
        }
        .header {
            text-align: center;
        }
        .header h1 {
            color: {{ $color }};
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            color: #1e3a8a; /* Azul oscuro */
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ── META TABLE ── */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #cbd5e1;
        }
        .meta-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            font-size: 7pt;
        }
        .meta-value {
            font-weight: bold;
            color: #0f172a;
            font-size: 7.5pt;
        }

        /* ── TIMETABLE ── */
        .timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }
        .timetable th {
            background-color: #f1f5f9;
            color: #1e3a8a; /* Azul oscuro para los días */
            font-weight: bold;
            font-size: 7.5pt;
            padding: 8px 4px;
            border: 1px solid #cbd5e1;
            text-align: center;
            text-transform: uppercase;
        }
        .timetable td {
            border: 1px dashed #cbd5e1;
            padding: 0;
            vertical-align: top;
        }
        /* Hora cell */
        .timetable .td-hora {
            background-color: #f8fafc;
            border-right: 1px solid #cbd5e1;
            border-bottom: 1px dashed #cbd5e1;
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            width: 70px;
        }
        .hora-start { font-weight: bold; color: #0f172a; font-size: 7.5pt; }
        .hora-end { color: #1e3a8a; font-size: 7.5pt; font-weight: bold; } /* Azul oscuro */

        /* Class card */
        .class-card {
            border-left: 3px solid {{ $color }};
            background-color: {{ $colorBg }};
            padding: 4px;
        }
        .c-sub {
            color: {{ $color }};
            font-weight: bold;
            font-size: 7.5pt;
            margin-bottom: 3px;
            line-height: 1.1;
        }
        .c-tch {
            color: #3b82f6; /* Azul brillante como en la imagen */
            font-weight: bold;
            font-size: 6.5pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .c-rm {
            color: #64748b;
            font-size: 6.5pt;
        }

        /* Receso */
        .receso-container {
            padding: 2px;
        }
        .receso {
            background-color: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 2px;
            text-align: center;
            color: #d97706;
            font-weight: bold;
            font-size: 7.5pt;
            width: 100%;
            padding: 4px 0;
        }

        /* ── LEGEND ── */
        .legend-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
            margin-top: 15px;
        }
        .legend-table th {
            background-color: {{ $colorBg }};
            color: {{ $color }};
            font-weight: bold;
            padding: 2px 4px;
            text-align: left;
            font-size: 6.5pt;
            border-bottom: 1px solid #cbd5e1;
            line-height: 1.1;
        }
        .legend-table td {
            padding: 2px 4px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 6.5pt;
            font-weight: bold;
            color: #334155;
            line-height: 1.1;
        }
        .badge {
            background-color: {{ $color }};
            color: white;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 6pt;
            margin-right: 4px;
            display: inline-block;
        }

        /* ── FOOTER ── */
        .footer {
            width: 100%;
            margin-top: 25px;
            font-size: 7pt;
            border-collapse: collapse;
        }
        .footer td {
            vertical-align: bottom;
        }
        .firma-line {
            border-top: 1px solid #94a3b8;
            width: 250px;
            text-align: center;
            padding-top: 4px;
            font-weight: bold;
            color: #475569;
            margin-left: 50px;
            font-size: 7.5pt;
        }
        .firma-sub {
            font-size: 6.5pt;
            font-weight: normal;
            color: #64748b;
        }
        .gen-txt {
            text-align: right;
            color: #94a3b8;
            font-size: 6.5pt;
        }
    </style>
</head>
<body>

    <div class="top-bar"></div>

    <table class="header-container">
        <tr>
            <td style="width: 15%; text-align: left;">
                @if(isset($logoBase64) && $logoBase64)
                    <img src="{{ $logoBase64 }}" style="max-height: 45px; max-width: 120px; object-fit: contain;" alt="Logo">
                @endif
            </td>
            <td style="width: 70%;">
                <div class="header">
                    <h1>{{ $config->nombre ?? 'Instituto Universitario de Tecnología Para la Informática' }}</h1>
                    <h2>Horario de Clases</h2>
                </div>
            </td>
            <td style="width: 15%;"></td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-label" style="width: 10%;">PERÍODO</td>
            <td class="meta-value" style="width: 45%;" colspan="3">
                {{ optional($seccion->periodoAcademico)->codigo ?? 'N/A' }}
                @if($seccion->periodoAcademico && $seccion->periodoAcademico->fecha_inicio)
                    <span style="font-weight: normal; color: #64748b;">
                        (Del {{ $seccion->periodoAcademico->fecha_inicio->format('d/m/Y') }} al {{ optional($seccion->periodoAcademico->fecha_fin)->format('d/m/Y') }} - {{ $seccion->periodoAcademico->duracion_semanas }} semanas)
                    </span>
                @endif
            </td>
            <td class="meta-label" style="width: 10%;">CARRERA</td>
            <td class="meta-value" style="width: 35%;" colspan="2">{{ optional($seccion->carrera)->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label" style="width: 10%;">SECCIÓN</td>
            <td class="meta-value" style="width: 20%;">{{ $seccion->codigo }}</td>
            <td class="meta-label" style="width: 10%;">SEMESTRE</td>
            <td class="meta-value" style="width: 15%;">{{ $seccion->semestre }}</td>
            <td class="meta-label" style="width: 10%; border-left: 1px solid #e2e8f0;">TURNO</td>
            <td class="meta-value" style="width: 35%;">{{ optional($seccion->turno)->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    @php $skipRows = []; foreach($dias as $d) $skipRows[$d] = 0; @endphp
    <table class="timetable">
        <thead>
            <tr>
                <th style="width: 70px;">HORA</th>
                @foreach($dias as $dia)
                    <th>{{ $dia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($bloques as $bloque)
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
                                $inicio = \Carbon\Carbon::parse($asignado['hora_inicio']);
                                $fin = \Carbon\Carbon::parse($asignado['hora_fin']);
                                $diff = $inicio->diffInMinutes($fin);
                                $rowspan = max(1, (int) ceil($diff / 40));
                                
                                if ($rowspan > 1) {
                                    $skipRows[$dia] = $rowspan - 1;
                                }
                                $pal = $materiaColors[$asignado['materia_id']] ?? ['bg'=>'#f1f5f9','border'=>'#94a3b8','text'=>'#334155'];
                            @endphp
                            @if($asignado['es_receso'])
                                <td rowspan="{{ $rowspan }}" style="background-color: #fffbeb; border: 1px dashed #f59e0b; text-align: center; vertical-align: middle; padding: 4px;">
                                    <span style="color: #d97706; font-weight: bold; font-size: 7.5pt;">RECESO (20 MIN)</span>
                                </td>
                            @else
                                <td rowspan="{{ $rowspan }}" style="background-color: {{ $pal['bg'] }}; border-left: 3px solid {{ $pal['border'] }}; border-right: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1; padding: 5px; vertical-align: top;">
                                    <div style="color: {{ $pal['text'] }}; font-weight: bold; font-size: 7.5pt; margin-bottom: 3px; line-height: 1.1;">{{ $asignado['materia_nombre'] }}</div>
                                    <div style="color: #3b82f6; font-weight: bold; font-size: 6.5pt; text-transform: uppercase; margin-bottom: 2px;">{{ $asignado['profesor_apellido'] }}, {{ $asignado['profesor_nombre'] }}</div>
                                    <div style="color: #64748b; font-size: 6.5pt;">Aula: {{ $asignado['espacio_codigo'] }}</div>
                                </td>
                            @endif
                        @else
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $leyenda = [];
        foreach ($horariosAsignados as $asig) {
            if (!$asig['es_receso'] && $asig['materia_nombre']) {
                $mid = $asig['materia_id'];
                if (!isset($leyenda[$mid])) {
                    $leyenda[$mid] = [
                        'codigo'   => $asig['materia_codigo'],
                        'materia'  => $asig['materia_nombre'],
                        'profesor' => $asig['profesor_nombre'] . ' ' . $asig['profesor_apellido'],
                        'telefono' => $asig['profesor_telefono'],
                    ];
                }
            }
        }
    @endphp

    @if(count($leyenda) > 0)
        <table class="legend-table">
            <thead>
                <tr>
                    <th style="width: 35%;">ASIGNATURA</th>
                    <th style="width: 40%;">DOCENTE</th>
                    <th style="width: 25%;">TELÉFONO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leyenda as $item)
                    <tr>
                        <td>
                            <span class="badge">{{ $item['codigo'] }}</span>
                            {{ $item['materia'] }}
                        </td>
                        <td style="font-weight: normal;">{{ $item['profesor'] }}</td>
                        <td style="font-weight: normal;">{{ $item['telefono'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="footer">
        <tr>
            <td style="width: 50%;">
                <div class="firma-line">
                    Coordinador del Departamento<br>
                    <span class="firma-sub">
                        Coordinación @if(isset($departamento)) de {{ $departamento->nombre }} @endif
                    </span>
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <div class="gen-txt">
                    Generado automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}
                    @if(isset($config->pie_pagina_pdf) && $config->pie_pagina_pdf)
                        <br>{{ $config->pie_pagina_pdf }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
