<x-filament-panels::page>

    {{-- ─── Panel de Filtros ────────────────────────────────── --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Filtros</p>
        <form wire:submit.prevent>{{ $this->form }}</form>
        <p class="mt-2 text-xs text-gray-400">El período académico es opcional. Si no lo seleccionas, se muestran todos los horarios del espacio en cualquier período.</p>
    </div>

    @if($this->espacio_id)
        @php
            $horarios = $this->getHorariosProperty();
            $espacio  = \App\Models\Espacio::find($this->espacio_id);
            $periodo  = $this->periodo_academico_id ? \App\Models\PeriodoAcademico::find($this->periodo_academico_id) : null;
            $libre    = $horarios->isEmpty();
        @endphp

        {{-- ─── Encabezado ────────────────────────────────────── --}}
        <div class="mt-8 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Espacio <span class="text-primary-600 dark:text-primary-400">{{ $espacio?->codigo }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ $espacio?->tipoEspacio?->nombre }} &middot; Capacidad {{ $espacio?->capacidad_maxima }}
                        @if($periodo)
                            &mdash; Período: <strong class="text-gray-900 dark:text-white">{{ $periodo->codigo }}</strong>
                        @endif
                    </p>
                </div>

                <div class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                    @if($libre)
                        ✓ DISPONIBLE
                    @else
                        CON ASIGNACIONES
                    @endif
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                @if($libre)
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">Sin horarios — completamente libre</span>
                @else
                    <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $horarios->count() }} horario(s) registrado(s)</span>
                @endif
            </div>
        </div>

        {{-- ─── Cuadrícula por Días (Kanban Style) ──────────────────────────── --}}
        @if($libre)
            <div class="mt-4 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-10 text-center text-gray-500 dark:text-gray-400">
                Este espacio no tiene ningún horario asignado{{ $periodo ? ' en el período ' . $periodo->codigo : '' }}.
            </div>
        @else
            @php
                $horariosPorDia = $horarios->groupBy('dia_semana');
            @endphp
            
            <div class="mt-6 overflow-x-auto pb-4">
                <div class="flex gap-4 min-w-max">
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                        @php
                            $clasesDelDia = $horariosPorDia->get($dia, collect());
                        @endphp
                        
                        <div class="w-72 flex flex-col gap-3 bg-gray-50/50 dark:bg-gray-900/50 p-3 rounded-2xl border border-gray-100 dark:border-gray-800">
                            <!-- Cabecera del día -->
                            <div class="sticky top-0 z-10 py-1 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $clasesDelDia->isEmpty() ? 'bg-emerald-400' : 'bg-primary-500' }}"></span>
                                    {{ $dia }}
                                </h3>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $clasesDelDia->isEmpty() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    {{ $clasesDelDia->count() }}
                                </span>
                            </div>
                            
                            <!-- Lista de Tarjetas -->
                            @if($clasesDelDia->isEmpty())
                                <div class="flex-1 rounded-xl border-2 border-dashed border-emerald-200 dark:border-emerald-900/30 bg-emerald-50/30 dark:bg-emerald-900/10 text-center text-emerald-600/70 dark:text-emerald-500/50 text-sm flex flex-col items-center justify-center min-h-[120px] shadow-sm">
                                    <x-heroicon-o-check-badge class="mb-1 opacity-50" style="width: 1.5rem; height: 1.5rem;" />
                                    <span class="font-medium">Día Libre</span>
                                </div>
                            @else
                                <div class="flex flex-col gap-3 flex-1">
                                    @foreach($clasesDelDia as $h)
                                        <div class="p-3.5 rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 relative group hover:shadow-md hover:ring-primary-500/50 dark:hover:ring-primary-500/50 transition-all cursor-default">
                                            <!-- Borde izquierdo de acento -->
                                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary-500 rounded-l-xl opacity-90 group-hover:bg-primary-400"></div>
                                            
                                            <div class="pl-2">
                                                <!-- Hora y Semestre -->
                                                <div class="flex justify-between items-start mb-2">
                                                    <div class="text-[11px] font-mono font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700/50 px-2 py-1 rounded-md border border-gray-200 dark:border-gray-600/50">
                                                        {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('h:i A') }}
                                                    </div>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded-md ring-1 ring-primary-500/20">
                                                        S: {{ $h->seccion?->semestre ?? '—' }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Materia -->
                                                <div class="font-bold text-sm text-gray-900 dark:text-white mt-1 leading-tight line-clamp-2" title="{{ $h->materia?->nombre }}">
                                                    {{ $h->materia?->nombre ?? '—' }}
                                                </div>
                                                
                                                <hr class="my-2 border-gray-100 dark:border-gray-700">
                                                
                                                <!-- Metadatos -->
                                                <div class="flex flex-col gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                                    <div class="flex items-center gap-2">
                                                        <x-heroicon-s-user class="text-gray-400 dark:text-gray-500 flex-shrink-0" style="width: 0.875rem; height: 0.875rem;" />
                                                        <span class="truncate font-medium">{{ $h->profesor?->nombre ?? '—' }} {{ $h->profesor?->apellido ?? '' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <x-heroicon-s-academic-cap class="text-gray-400 dark:text-gray-500 flex-shrink-0" style="width: 0.875rem; height: 0.875rem;" />
                                                        <span class="truncate">{{ $h->seccion?->codigo ?? '—' }} • {{ $h->materia?->carrera?->siglas ?? $h->materia?->carrera?->nombre ?? '—' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else
        {{-- ─── Estado inicial sin ícono grande ────────────────── --}}
        <div class="mt-6 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-12 text-center">
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Selecciona un Espacio</p>
            <p class="text-sm text-gray-400 mt-1">Elige un aula (y opcionalmente un período) para ver su ocupación y disponibilidad.</p>
        </div>
    @endif

</x-filament-panels::page>
