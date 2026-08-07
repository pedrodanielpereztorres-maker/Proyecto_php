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

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .timetable-card {
                break-inside: avoid;
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }
            .fi-sidebar, .fi-topbar {
                display: none !important;
            }
        }
    </style>

    <div class="no-print">
        <form wire:submit.prevent="submit">
            {{ $this->form }}
        </form>
    </div>

    @if($periodo_academico_id && $seccion_id)
        <div class="mt-8 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden printable-area">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-o-calendar-days class="text-primary-500 flex-shrink-0" style="width: 1.5rem; height: 1.5rem;" />
                        Horario Académico
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ \App\Models\Seccion::find($seccion_id)->codigo ?? '' }}</span>
                        &bull; Lunes a Viernes &bull; Bloques de 40 min
                    </p>
                </div>
                <a href="{{ route('horarios.pdf', ['seccion_id' => $seccion_id]) }}" target="_blank" class="no-print bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors shadow-sm">
                    <x-heroicon-m-document-arrow-down class="flex-shrink-0" style="width: 1rem; height: 1rem;" />
                    Guardar como PDF
                </a>
            </div>
            
            <div class="overflow-auto relative z-10 p-4 max-h-[65vh]" style="scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent;">
                <table class="w-full text-sm text-left border-separate" style="border-spacing: 4px;">
                    <thead class="sticky top-0 z-30">
                        <tr>
                            <th class="px-3 py-3 w-32 text-center text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 sticky left-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-xl shadow-sm">Hora</th>
                            @foreach($dias as $dia)
                                <th class="px-4 py-3 text-center w-48 text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-300 bg-gray-50/90 dark:bg-gray-800/90 backdrop-blur-md rounded-xl shadow-sm">{{ $dia }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                            $skipRows = [];
                            foreach ($dias as $d) $skipRows[$d] = 0;
                            
                            // Paleta de colores usando clases personalizadas
                            $themeKeys = ['theme-blue', 'theme-emerald', 'theme-purple', 'theme-rose', 'theme-amber'];
                        @endphp
                        @foreach($this->bloques as $index => $bloque)
                            <tr class="group/row" wire:key="row-{{ $index }}">
                                <td class="px-2 py-3 text-xs text-center align-middle relative sticky left-0 z-20 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-xl shadow-[4px_0_10px_-3px_rgba(0,0,0,0.05)] border border-gray-100 dark:border-gray-800" wire:key="time-{{ $index }}">
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $bloque['inicio_ampm'] }}</span><br>
                                    <span class="font-bold text-gray-500 dark:text-gray-400">{{ $bloque['fin_ampm'] }}</span>
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
                                            $durationMins = \Carbon\Carbon::parse($asignado->hora_inicio)->diffInMinutes(\Carbon\Carbon::parse($asignado->hora_fin));
                                            $rowspan = max(1, (int) round($durationMins / 40));
                                            $skipRows[$dia] = $rowspan - 1;
                                        @endphp
                                        <td class="relative align-top p-0.5" rowspan="{{ $rowspan }}" wire:key="cell-{{ $index }}-{{ $dia }}">
                                            @if($asignado->es_receso)
                                                <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700/50 rounded-xl p-2 h-full w-full flex justify-between items-center group shadow-sm hover:shadow-md transition-all relative overflow-hidden {{ $rowspan > 1 ? 'min-h-[5rem]' : 'min-h-[2.5rem]' }}">
                                                    <!-- Stripe Pattern -->
                                                    <div class="absolute inset-0 recess-pattern"></div>
                                                    
                                                    <div class="flex items-center gap-1.5 font-bold text-amber-700 dark:text-amber-500 text-xs w-full justify-center relative z-10 no-print">
                                                        <x-heroicon-s-clock class="text-amber-500 flex-shrink-0" style="width: 1rem; height: 1rem;" />
                                                        <span class="tracking-widest uppercase">Receso (20 min)</span>
                                                    </div>
                                                    <div class="absolute top-1/2 -translate-y-1/2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-full shadow-sm z-20 hover:scale-110 no-print">
                                                        {{ ($this->eliminarBloqueAction)(['id' => $asignado->id]) }}
                                                    </div>
                                                    @php
                                                        $finReceso = \Carbon\Carbon::parse($asignado->hora_fin)->format('H:i');
                                                        $nextKey = $dia . '_' . $finReceso;
                                                        $nextAsignado = $horariosAsignados[$nextKey] ?? null;
                                                    @endphp
                                                    @if(!$nextAsignado)
                                                        <div class="absolute inset-0 flex items-center justify-center opacity-50 hover:opacity-100 transition-opacity bg-gray-50/30 dark:bg-gray-800/30 backdrop-blur-[1px] no-print">
                                                            <div class="flex gap-2 bg-white/90 dark:bg-gray-800/90 p-1.5 rounded-full shadow-sm border border-gray-100 dark:border-gray-700 hover:scale-110 transition-transform">
                                                                <div title="Añadir clase después del receso">
                                                                    {{ ($this->asignarBloqueAction)(['dia' => $dia, 'inicio' => $finReceso, 'fin' => '']) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                @php
                                                    $themeClass = $themeKeys[$asignado->materia_id % count($themeKeys)];
                                                @endphp
                                                <div class="timetable-card {{ $themeClass }} group">
                                                    <!-- Colored Left Indicator -->
                                                    <div class="theme-indicator"></div>
                                                    
                                                    <div class="relative z-10">
                                                        <div class="font-extrabold theme-title leading-tight text-sm mb-2 drop-shadow-sm">
                                                            {{ $asignado->materia->nombre }}
                                                        </div>
                                                        <div class="text-xs text-gray-700 dark:text-gray-300 flex items-center gap-1.5 bg-white/60 dark:bg-black/30 rounded-md px-1.5 py-1 mb-1 backdrop-blur-sm w-fit border border-white/20 dark:border-black/20">
                                                            <x-heroicon-s-user class="theme-icon flex-shrink-0" style="width: 0.875rem; height: 0.875rem;" />
                                                            <span class="truncate font-medium" title="{{ $asignado->profesor->nombre }} {{ $asignado->profesor->apellido }}">{{ $asignado->profesor->nombre }} {{ $asignado->profesor->apellido }}</span>
                                                        </div>
                                                        <div class="text-[11px] text-gray-600 dark:text-gray-400 flex items-center gap-1.5 px-1.5 mt-1">
                                                            <x-heroicon-s-map-pin class="theme-icon flex-shrink-0 opacity-90" style="width: 0.875rem; height: 0.875rem;" />
                                                            <span class="font-bold truncate" title="{{ $asignado->espacio->codigo }}">{{ $asignado->espacio->codigo }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm rounded-full shadow-sm z-20 hover:scale-110 cursor-pointer no-print">
                                                        {{ ($this->eliminarBloqueAction)(['id' => $asignado->id]) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @else
                                        @php
                                            $nextBloque = $this->bloques[$index + 1] ?? null;
                                            $nextKey = $nextBloque ? $dia . '_' . $nextBloque['inicio'] : null;
                                            $nextAsignado = $nextKey ? ($horariosAsignados[$nextKey] ?? null) : null;
                                        @endphp
                                        
                                        @if($index % 2 == 0 && !$nextAsignado)
                                            @php $skipRows[$dia] = 1; @endphp
                                            <td rowspan="2" class="p-1 align-middle h-24" wire:key="cell-{{ $index }}-{{ $dia }}">
                                                <div class="h-full w-full flex flex-col items-center justify-center gap-2 opacity-0 group-hover/row:opacity-100 transition-all duration-300 bg-transparent hover:bg-gray-50/80 dark:hover:bg-gray-800/40 rounded-xl border-2 border-transparent hover:border-dashed hover:border-gray-300 dark:hover:border-gray-600">
                                                    <div title="Asignar Materia (40 min)" class="transform hover:scale-110 transition-transform">
                                                        {{ ($this->asignarBloqueAction)(['dia' => $dia, 'inicio' => $bloque['inicio'], 'fin' => '']) }}
                                                    </div>
                                                    <div title="Asignar Receso (20 min)" class="transform hover:scale-110 transition-transform">
                                                        {{ ($this->asignarRecesoAction)(['dia' => $dia, 'inicio' => $bloque['inicio'], 'fin' => '']) }}
                                                    </div>
                                                </div>
                                            </td>
                                        @else
                                            <td class="p-1 align-middle h-12" wire:key="cell-{{ $index }}-{{ $dia }}">
                                                <div class="h-full w-full flex items-center justify-center gap-2 opacity-0 group-hover/row:opacity-100 transition-all duration-300 bg-transparent hover:bg-gray-50/80 dark:hover:bg-gray-800/40 rounded-xl border-2 border-transparent hover:border-dashed hover:border-gray-300 dark:hover:border-gray-600">
                                                    <div title="Asignar Materia (40 min)" class="transform hover:scale-110 transition-transform">
                                                        {{ ($this->asignarBloqueAction)(['dia' => $dia, 'inicio' => $bloque['inicio'], 'fin' => '']) }}
                                                    </div>
                                                    <div title="Asignar Receso (20 min)" class="transform hover:scale-110 transition-transform">
                                                        {{ ($this->asignarRecesoAction)(['dia' => $dia, 'inicio' => $bloque['inicio'], 'fin' => '']) }}
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="mt-8 flex flex-col items-center justify-center p-12 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 text-center">
            <x-heroicon-o-calendar class="text-gray-300 dark:text-gray-600 mb-4" style="width: 4rem; height: 4rem; margin: 0 auto;" />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Seleccione los filtros para comenzar</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Elija un período académico y una sección para visualizar la grilla de horarios.</p>
        </div>
    @endif
    
    <x-filament-actions::modals />
</x-filament-panels::page>
