<x-filament-panels::page>

    {{-- ─── Panel de Filtros ────────────────────────────────── --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Filtros</p>
        <form wire:submit.prevent>{{ $this->form }}</form>
        <p class="mt-2 text-xs text-gray-400">El semestre es opcional. Si no lo seleccionas, se muestran todos los horarios del aula.</p>
    </div>

    @if($this->aula_id)
        @php
            $horarios = $this->getHorariosProperty();
            $aula     = \App\Models\Aula::find($this->aula_id);
            $sem      = $this->semestre_id ? \App\Models\Semestre::find($this->semestre_id) : null;
            $libre    = $horarios->isEmpty();
        @endphp

        {{-- ─── Encabezado ────────────────────────────────────── --}}
        <div class="mt-5 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Aula <span class="text-primary-600 dark:text-primary-400">{{ $aula?->codigo }}</span>
                    <span class="ml-2 text-sm font-normal text-gray-400">
                        {{ $aula?->tipo }} &middot; Cap. {{ $aula?->capacidad }}
                    </span>
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    @if($sem) Semestre: <strong>{{ $sem->nombre }}</strong> &mdash; @endif
                    @if($libre)
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Sin horarios — completamente libre</span>
                    @else
                        <span class="font-semibold text-amber-600 dark:text-amber-400">{{ $horarios->count() }} horario(s) registrado(s)</span>
                    @endif
                </p>
            </div>
            @if($libre)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">✓ DISPONIBLE</span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">CON ASIGNACIONES</span>
            @endif
        </div>

        {{-- ─── Tabla o mensaje vacío ──────────────────────────── --}}
        @if($libre)
            <div class="mt-4 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-10 text-center text-gray-500 dark:text-gray-400">
                Esta aula no tiene ningún horario asignado{{ $sem ? ' en el semestre ' . $sem->nombre : '' }}.
            </div>
        @else
            <div class="mt-4 rounded-xl overflow-hidden ring-1 ring-gray-200 dark:ring-white/10">
                <table class="w-full text-sm bg-white dark:bg-gray-900">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Semestre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Día</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Horario</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Materia</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Profesor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Carrera</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($horarios as $h)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                        {{ $h->semestre?->nombre ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        @if(in_array($h->dia_semana, ['Lunes','Miércoles','Viernes']))
                                            bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                                        @else
                                            bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300
                                        @endif">
                                        {{ $h->dia_semana }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                    {{ $h->materia?->nombre ?? '—' }}
                                    @if($h->materia?->codigo)
                                        <span class="text-gray-400 text-xs">({{ $h->materia->codigo }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                    {{ $h->profesor?->nombre ?? '—' }} {{ $h->profesor?->apellido ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $h->materia?->carrera?->nombre ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else
        {{-- ─── Estado inicial sin ícono grande ────────────────── --}}
        <div class="mt-6 rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-12 text-center">
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Selecciona un Aula</p>
            <p class="text-sm text-gray-400 mt-1">Elige un aula (y opcionalmente un semestre) para ver su ocupación y disponibilidad.</p>
        </div>
    @endif

</x-filament-panels::page>
