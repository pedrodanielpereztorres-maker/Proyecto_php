<x-filament-panels::page>

    {{-- ─── Panel de Filtros ────────────────────────────────── --}}
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-white/10 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-4">Filtros de Búsqueda</p>
        <form wire:submit.prevent>{{ $this->form }}</form>
    </div>

    @if($this->profesor_id)
        @php
            $horarios = $this->getHorariosProperty();
            $prof     = \App\Models\Profesor::find($this->profesor_id);
            $sem      = $this->semestre_id ? \App\Models\Semestre::find($this->semestre_id) : null;
        @endphp

        {{-- ─── Encabezado del resultado ────────────────────── --}}
        <div class="mt-8 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $prof?->nombre }} {{ $prof?->apellido }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-2">
                        Horario de clases
                        @if($sem) &mdash; {{ $sem->nombre }} @endif
                    </p>
                </div>
                <div class="rounded-full border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                    {{ $horarios->count() }} clase(s) asignada(s)
                </div>
            </div>
        </div>

        {{-- ─── Tabla ────────────────────────────────────────── --}}
        @if($horarios->isEmpty())
            <div class="mt-4 rounded-2xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-white/10 py-10 px-6 text-center text-gray-500 dark:text-gray-400">
                Este profesor no tiene horarios asignados{{ $sem ? ' en el semestre ' . $sem->nombre : '' }}.
            </div>
        @else
            <div class="mt-4 rounded-2xl overflow-hidden ring-1 ring-gray-200 dark:ring-white/10 shadow-sm">
                <table class="w-full text-sm bg-white dark:bg-gray-900">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr class="border-b border-gray-200 dark:border-white/10">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Día</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Horario</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Materia (Código)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Carrera</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Aula</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($horarios as $h)
                            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                {{-- Día --}}
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
                                {{-- Horario --}}
                                <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}
                                </td>
                                {{-- Materia --}}
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">
                                    {{ $h->materia?->nombre ?? '—' }}
                                    @if($h->materia?->codigo)
                                        <span class="text-gray-400 text-xs">({{ $h->materia->codigo }})</span>
                                    @endif
                                </td>
                                {{-- Carrera --}}
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                    {{ $h->materia?->carrera?->nombre ?? '—' }}
                                </td>
                                {{-- Aula --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-bold ring-1 ring-gray-200 dark:ring-gray-700">
                                        {{ $h->aula?->codigo ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @else
        <div class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-white/10 dark:bg-gray-900/95 p-8 text-center max-w-xl mx-auto">
            <p class="text-base font-semibold text-gray-700 dark:text-gray-300">Selecciona un Profesor</p>
            <p class="text-sm text-gray-400 mt-1">Elige el semestre y el profesor para ver su horario completo de clases.</p>
        </div>
    @endif

</x-filament-panels::page>
