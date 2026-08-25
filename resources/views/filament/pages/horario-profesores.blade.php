<x-filament-panels::page>
    <style>
        /* Custom Premium UI Styles - Bypasses Tailwind Compilation */
        .timetable-card {
            border-radius: 0.75rem;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 5rem;
        }
        .timetable-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .theme-indicator {
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            opacity: 0.7;
        }
        
        /* Vibrant Themes */
        .theme-blue { background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; }
        .theme-blue .theme-indicator { background-color: #3b82f6; }
        .theme-blue .theme-title { color: #1e3a8a; }
        .theme-blue .theme-icon { color: #3b82f6; }
        
        .theme-emerald { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0; }
        .theme-emerald .theme-indicator { background-color: #10b981; }
        .theme-emerald .theme-title { color: #064e3b; }
        .theme-emerald .theme-icon { color: #10b981; }
        
        .theme-purple { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border: 1px solid #e9d5ff; }
        .theme-purple .theme-indicator { background-color: #a855f7; }
        .theme-purple .theme-title { color: #581c87; }
        .theme-purple .theme-icon { color: #a855f7; }
        
        .theme-rose { background: linear-gradient(135deg, #fff1f2, #ffe4e6); border: 1px solid #fecdd3; }
        .theme-rose .theme-indicator { background-color: #f43f5e; }
        .theme-rose .theme-title { color: #881337; }
        .theme-rose .theme-icon { color: #f43f5e; }
        
        .theme-amber { background: linear-gradient(135deg, #fffbeb, #fef3c7); border: 1px solid #fde68a; }
        .theme-amber .theme-indicator { background-color: #f59e0b; }
        .theme-amber .theme-title { color: #78350f; }
        .theme-amber .theme-icon { color: #f59e0b; }

        /* Dark Mode Adjustments */
        .dark .theme-blue { background: linear-gradient(135deg, rgba(30,58,138,0.4), rgba(30,64,175,0.2)); border-color: rgba(30,58,138,0.8); }
        .dark .theme-blue .theme-title { color: #93c5fd; }
        
        .dark .theme-emerald { background: linear-gradient(135deg, rgba(6,78,59,0.4), rgba(4,120,87,0.2)); border-color: rgba(6,78,59,0.8); }
        .dark .theme-emerald .theme-title { color: #6ee7b7; }
        
        .dark .theme-purple { background: linear-gradient(135deg, rgba(88,28,135,0.4), rgba(107,33,168,0.2)); border-color: rgba(88,28,135,0.8); }
        .dark .theme-purple .theme-title { color: #d8b4fe; }
        
        .dark .theme-rose { background: linear-gradient(135deg, rgba(136,19,55,0.4), rgba(159,18,57,0.2)); border-color: rgba(136,19,55,0.8); }
        .dark .theme-rose .theme-title { color: #fda4af; }
        
        .dark .theme-amber { background: linear-gradient(135deg, rgba(120,53,15,0.4), rgba(146,64,14,0.2)); border-color: rgba(120,53,15,0.8); }
        .dark .theme-amber .theme-title { color: #fcd34d; }

        .recess-pattern {
            background-image: repeating-linear-gradient(45deg, rgba(0,0,0,0.03) 0, rgba(0,0,0,0.03) 1px, transparent 0, transparent 50%);
            background-size: 10px 10px;
        }
        .dark .recess-pattern {
            background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 1px, transparent 0, transparent 50%);
        }
    </style>

    {{-- ─── Panel de Filtros ────────────────────────────────── --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Filtros de Búsqueda</p>
        <form wire:submit.prevent>{{ $this->form }}</form>
    </div>

    @if($this->profesor_id)
        @php
            $horarios = $this->getHorariosProperty();
            $prof     = \App\Models\Profesor::find($this->profesor_id);
            $periodo  = $this->periodo_academico_id ? \App\Models\PeriodoAcademico::find($this->periodo_academico_id) : null;
            $libre    = $horarios->isEmpty();
        @endphp

        {{-- ─── Encabezado ────────────────────────────────────── --}}
        <div class="mt-8 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2 flex-wrap">
                        <span>{{ $prof?->nombre }} {{ $prof?->apellido }}</span>
                        @if($prof?->cedula)
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-2.5 py-0.5 rounded-full">C.I. {{ $prof->cedula }}</span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Horario de clases
                        @if($periodo) &mdash; Período: <strong class="text-gray-900 dark:text-white">{{ $periodo->codigo }}</strong> @endif
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                        {{ $horarios->count() }} clase(s) asignada(s)
                    </div>
                    <a href="{{ route('profesores.pdf', ['profesor_id' => $this->profesor_id, 'periodo_academico_id' => $this->periodo_academico_id]) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimir Horario PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ─── Cuadrícula Tipo Horario (Timetable) ──────────────────────────── --}}
        @if($libre)
            <div class="mt-4 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-10 text-center text-gray-500 dark:text-gray-400">
                Este profesor no tiene ningún horario asignado{{ $periodo ? ' en el período ' . $periodo->codigo : '' }}.
            </div>
        @else
            @php
                $horaMinima = null;
                $horaMaxima = null;
                $horariosAsignados = [];
                foreach ($horarios as $h) {
                    if (!$h->hora_inicio) continue;
                    $key = $h->dia_semana . '_' . \Carbon\Carbon::parse($h->hora_inicio)->format('H:i');
                    $horariosAsignados[$key] = $h;
                    
                    $hi = \Carbon\Carbon::parse($h->hora_inicio);
                    $hf = \Carbon\Carbon::parse($h->hora_fin);
                    if (!$horaMinima || $hi->lt($horaMinima)) $horaMinima = $hi->copy();
                    if (!$horaMaxima || $hf->gt($horaMaxima)) $horaMaxima = $hf->copy();
                }
                
                $bloquesVisibles = $this->bloques;
                if ($horaMinima && $horaMaxima) {
                    $bloquesVisibles = array_values(array_filter($this->bloques, function ($bloque) use ($horaMinima, $horaMaxima) {
                        $bIni = \Carbon\Carbon::parse($bloque['inicio']);
                        return $bIni->gte($horaMinima) && $bIni->lt($horaMaxima);
                    }));
                }
                if (empty($bloquesVisibles)) {
                    $bloquesVisibles = $this->bloques;
                }
                
                $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                $skipRows = [];
                foreach ($dias as $d) $skipRows[$d] = 0;
                
                $themeKeys = ['theme-blue', 'theme-emerald', 'theme-purple', 'theme-rose', 'theme-amber'];
            @endphp
            
            <div class="mt-6 overflow-auto relative z-10 p-2 max-h-[75vh]" style="scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent;">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="sticky top-0 z-30">
                        <tr>
                            <th class="px-3 py-3 w-28 text-center text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 sticky left-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">Hora</th>
                            @foreach($dias as $dia)
                                <th class="px-4 py-3 text-center min-w-[13rem] text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-300 bg-gray-50/90 dark:bg-gray-800/90 backdrop-blur-md rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">{{ $dia }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bloquesVisibles as $index => $bloque)
                            <tr>
                                <td class="px-2 py-3 text-[10px] sm:text-xs text-center align-middle relative sticky left-0 z-20 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-xl shadow-[4px_0_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 dark:border-gray-800">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $bloque['inicio_ampm'] }}</span><br>
                                    <span class="font-bold text-gray-400 dark:text-gray-500">{{ $bloque['fin_ampm'] }}</span>
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
                                            $horaFinAsig = \Carbon\Carbon::parse($asignado->hora_fin)->format('H:i');
                                            $rowspan = 0;
                                            for ($k = $index; $k < count($bloquesVisibles); $k++) {
                                                if ($bloquesVisibles[$k]['inicio'] < $horaFinAsig) {
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
                                        <td class="relative align-top p-1" rowspan="{{ $rowspan }}">
                                            @php
                                                $themeClass = $themeKeys[$asignado->materia_id % count($themeKeys)];
                                                $horaIniFmt = \Carbon\Carbon::parse($asignado->hora_inicio)->format('h:i A');
                                                $horaFinFmt = \Carbon\Carbon::parse($asignado->hora_fin)->format('h:i A');
                                            @endphp
                                            <div class="timetable-card {{ $themeClass }} h-full" style="min-height: {{ $rowspan * 65 }}px;">
                                                <div class="theme-indicator"></div>
                                                <div class="relative z-10 flex flex-col h-full justify-between">
                                                    <div>
                                                        <div class="font-extrabold theme-title leading-tight text-[12px] sm:text-sm mb-1 drop-shadow-sm">
                                                            {{ $asignado->materia->nombre }}
                                                        </div>
                                                        <div class="text-[11px] font-bold text-gray-800 dark:text-gray-200 mb-1.5 flex items-center gap-1">
                                                            <x-heroicon-m-clock class="flex-shrink-0 text-gray-500 dark:text-gray-400" style="width: 13px; height: 13px; min-width: 13px;" />
                                                            <span>{{ $horaIniFmt }} &ndash; {{ $horaFinFmt }}</span>
                                                        </div>
                                                        <div class="text-[10px] sm:text-[11px] text-gray-700 dark:text-gray-300 flex items-center gap-1.5 bg-white/60 dark:bg-black/30 rounded-md px-1.5 py-1 mb-1 backdrop-blur-sm w-fit border border-white/20 dark:border-black/20">
                                                            <span class="font-bold uppercase tracking-wider">Sec: {{ $asignado->seccion->codigo }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-[10px] sm:text-[11px] text-gray-600 dark:text-gray-400 flex flex-col gap-1 mt-2 bg-white/50 dark:bg-black/20 rounded p-1.5">
                                                        <div class="flex items-center gap-1.5 truncate">
                                                            <x-heroicon-s-academic-cap class="theme-icon flex-shrink-0 opacity-80" style="width: 0.875rem; height: 0.875rem;" />
                                                            <span class="truncate" title="{{ $asignado->materia->carrera->nombre }}">{{ $asignado->materia->carrera->siglas ?? $asignado->materia->carrera->nombre }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5 font-bold truncate text-gray-800 dark:text-gray-200">
                                                            <x-heroicon-s-building-office-2 class="theme-icon flex-shrink-0 opacity-90" style="width: 0.875rem; height: 0.875rem;" />
                                                            <span>Espacio: {{ $asignado->espacio->codigo }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @else
                                        <td class="p-1 align-middle h-16">
                                            <div class="h-full w-full min-h-[60px] rounded-xl bg-gray-50/50 dark:bg-gray-800/20 border border-dashed border-gray-200 dark:border-gray-800/50"></div>
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else
        <div class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900/95 p-8 text-center max-w-xl mx-auto">
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Selecciona un Profesor</p>
            <p class="text-sm text-gray-400 mt-1">Elige el período y el profesor para ver su horario completo de clases.</p>
        </div>
    @endif

</x-filament-panels::page>
