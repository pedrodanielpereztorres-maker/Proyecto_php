<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario Académico - {{ $seccion->codigo }}</title>
    @php
        $colorPrincipal = ($config && $config->color_principal) ? $config->color_principal : '#1e3a8a';
        $colorFondoTarjeta = $colorPrincipal . '15';
        $colorSecundario = '#f8fafc';
    @endphp
    <style>
        /* Modern Font for PDF */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 8px;
            color: #1f2937;
            background-color: #ffffff;
        }

        /* Top Brand Bar */
        .brand-bar {
            background-color: #2563eb; /* Royal Blue */
            height: 6px;
            width: 100%;
            margin-bottom: 15px;
        }

        /* Header Section */
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: #dc2626; /* IUTEPI Red */
            margin: 0;
            letter-spacing: -0.5px;
        }
        .title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }

        /* Meta Info Pills (Modern Look) */
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .meta-info td {
            padding: 6px 10px;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-info tr:last-child td {
            border-bottom: none;
        }
        .meta-label {
            font-weight: 700;
            color: #475569;
            width: 60px;
            text-transform: uppercase;
            font-size: 8px;
        }
        .meta-value {
            color: #0f172a;
            font-weight: 600;
        }

        /* Timetable Grid */
        .timetable-container {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .timetable th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            padding: 10px 4px;
            border-bottom: 2px solid #cbd5e1;
            border-right: 1px solid #e2e8f0;
        }
        .timetable th:last-child { border-right: none; }
        
        .timetable td {
            border-bottom: 1px dashed #e2e8f0;
            border-right: 1px dashed #e2e8f0;
            padding: 2px 3px;
            text-align: center;
            vertical-align: top;
            word-wrap: break-word;
        }
        .timetable tr:last-child td { border-bottom: none; }
        .timetable td:last-child { border-right: none; }
        
        .col-hora {
            width: 55px;
            background-color: #f8fafc;
            font-weight: 700;
            color: #475569;
            vertical-align: middle;
            font-size: 8px;
            border-right: 2px solid #cbd5e1 !important;
            text-align: center;
        }
        .col-hora .time-text {
            color: #0f172a;
            line-height: 1.2;
        }

        /* Class Cards (Modern UI) */
        .card {
            background-color: {{ $colorFondoTarjeta }};
            border-left: 3px solid {{ $colorPrincipal }};
            border-radius: 3px;
            padding: 6px 5px;
            margin-bottom: 0px;
            text-align: left;
            overflow: hidden;
        }
        .card-title {
            font-weight: 700;
            font-size: 9px;
            color: {{ $colorPrincipal }};
            margin-bottom: 3px;
            line-height: 1.3;
        }
        .card-subtitle {
            font-size: 8px;
            color: #3b82f6;
            margin-bottom: 1px;
            line-height: 1.2;
            font-weight: 600;
        }
        .card-room {
            font-size: 7px;
            color: #64748b;
        }

        /* Recess Styling */
        .receso {
            background-color: #fffbeb;
            color: #d97706;
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
            vertical-align: middle;
            border: 1px dashed #fcd34d;
            border-radius: 3px;
            font-size: 8px;
            overflow: hidden;
        }

        /* Legend */
        .legend-title {
            font-size: 9px;
            font-weight: 800;
            color: #1e40af; /* Dark blue */
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .legend-box {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 7.5px;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            overflow: hidden;
        }
        .legend-box th {
            background-color: #dbeafe;
            color: #1e3a8a;
            font-weight: 700;
            text-align: left;
            padding: 4px 6px;
            border-bottom: 1px solid #bfdbfe;
            text-transform: uppercase;
        }
        .legend-box td {
            padding: 4px 6px;
            border-bottom: 1px dashed #e2e8f0;
            color: #334155;
            background-color: #f8fafc;
        }
        .legend-box tr:last-child td {
            border-bottom: none;
        }
        .badge {
            background-color: #2563eb;
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            margin-right: 3px;
        }

        /* Footer */
        .footer-table {
            width: 100%;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .firma-line {
            width: 150px;
            border-top: 1px solid #94a3b8;
            text-align: center;
            padding-top: 4px;
            font-weight: 700;
            font-size: 8px;
            color: #475569;
            margin: 0 auto;
        }
        .footer-text {
            font-size: 7px;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="brand-bar"></div>

    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-container">
                    @if(isset($logoBase64))
                        <img src="{{ $logoBase64 }}" class="logo" alt="Logo Institucional">
                    @endif
                </td>
                <td class="title-container">
                    <h1>{{ $config->nombre ?? 'Horario Académico' }}</h1>
                    <h2>Programa de Formación y Distribución de Bloques</h2>
                </td>
                <td style="width: 80px;"></td> <!-- Espaciador para centrar el texto -->
            </tr>
        </table>
    </div>

    <table class="meta-info">
        <tr>
            <td class="meta-label">Período</td>
            <td class="meta-value">
                {{ $seccion->periodoAcademico->codigo ?? 'N/A' }} 
                @if($seccion->periodoAcademico)
                    <span style="font-weight: normal; color: #64748b; font-size: 8px;">
                        (Del {{ $seccion->periodoAcademico->fecha_inicio ? $seccion->periodoAcademico->fecha_inicio->format('d/m/Y') : 'N/A' }} al {{ $seccion->periodoAcademico->fecha_fin ? $seccion->periodoAcademico->fecha_fin->format('d/m/Y') : 'N/A' }} - {{ $seccion->periodoAcademico->duracion_semanas }} semanas)
                    </span>
                @endif
            </td>
            <td class="meta-label">Carrera</td>
            <td class="meta-value" colspan="3">{{ $seccion->carrera->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Sección</td>
            <td class="meta-value">{{ $seccion->codigo }}</td>
            <td class="meta-label">Semestre</td>
            <td class="meta-value">{{ $seccion->semestre }}</td>
            <td class="meta-label">Turno</td>
            <td class="meta-value">{{ $seccion->turno->nombre ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="timetable-container">
        <table class="timetable">
            <thead>
                <tr>
                    <th class="col-hora">Hora</th>
                    @foreach($dias as $dia)
                        <th>{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $skipRows = [];
                    foreach ($dias as $d) $skipRows[$d] = 0;
                @endphp
                @foreach($bloques as $index => $bloque)
                    <tr>
                        <td class="col-hora">
                            <div class="time-text">{{ $bloque['inicio_ampm'] }}</div>
                            <div class="time-text">{{ $bloque['fin_ampm'] }}</div>
                        </td>

                        @foreach($dias as $dia)
                            @if($skipRows[$dia] > 0)
                                @php $skipRows[$dia]--; @endphp
                                @continue
                            @endif

                            @php
                                $key = $dia . '_' . $bloque['inicio'];
                                $asignado = $horariosAsignados[$key] ?? null;
                                $rowspan = 1;
                            @endphp

                            @if($asignado)
                                @php
                                    $inicio = \Carbon\Carbon::parse($asignado->hora_inicio);
                                    $fin = \Carbon\Carbon::parse($asignado->hora_fin);
                                    $diff = $inicio->diffInMinutes($fin);
                                    $rowspan = max(1, (int) ceil($diff / 40));
                                    if ($rowspan > 1) {
                                        $skipRows[$dia] = $rowspan - 1;
                                    }
                                @endphp

                                <td rowspan="{{ $rowspan }}" style="padding: 2px;">
                                    @if($asignado->es_receso)
                                        <div class="receso" style="height: 24px; line-height: 24px;">
                                            RECESO (20 min)
                                        </div>
                                    @else
                                        <div class="card">
                                            <div class="card-title">{{ \Illuminate\Support\Str::limit($asignado->materia->nombre, 30) }}</div>
                                            <div class="card-subtitle">{{ explode(' ', $asignado->profesor->nombre)[0] }} {{ explode(' ', $asignado->profesor->apellido)[0] }}</div>
                                            <div class="card-room">Aula: {{ $asignado->espacio->codigo }}</div>
                                        </div>
                                    @endif
                                </td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Leyenda -->
    @php
        $leyenda = [];
        foreach ($horariosAsignados as $h) {
            if (!$h->es_receso && $h->materia && $h->profesor) {
                $materiaId = $h->materia->id;
                if (!isset($leyenda[$materiaId])) {
                    $leyenda[$materiaId] = [
                        'materia' => $h->materia->codigo . ' - ' . $h->materia->nombre,
                        'profesor' => $h->profesor->nombre . ' ' . $h->profesor->apellido,
                        'telefono' => $h->profesor->telefono ?? 'N/A'
                    ];
                }
            }
        }
    @endphp

    @if(count($leyenda) > 0)
        <div class="legend-title">Directorio Docente</div>
        <table class="legend-box">
            <thead>
                <tr>
                    <th style="width: 45%">Asignatura</th>
                    <th style="width: 35%">Profesor</th>
                    <th style="width: 20%">Teléfono</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leyenda as $item)
                    <tr>
                        <td style="font-weight: 600;"><span class="badge">{{ explode(' - ', $item['materia'])[0] }}</span> {{ explode(' - ', $item['materia'])[1] }}</td>
                        <td>{{ $item['profesor'] }}</td>
                        <td>{{ $item['telefono'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="footer-table">
        <tr>
            <td style="width: 33%; vertical-align: bottom;">
                <div class="firma-line">
                    Firma y Sello de Coordinación
                </div>
            </td>
            <td style="width: 67%; vertical-align: bottom;" class="footer-text">
                Generado automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y h:i A') }}
            </td>
        </tr>
    </table>

</body>
</html>
