<div class="space-y-4">
    @if($horarios->isEmpty())
        <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
            Esta sección no tiene bloques de horario asignados actualmente.
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-left text-xs">
                <thead class="border-b border-gray-200 bg-gray-50 text-gray-600 dark:border-gray-800 dark:bg-gray-800/50 dark:text-gray-300">
                    <tr>
                        <th class="px-3 py-2.5 font-semibold">Día y Hora</th>
                        <th class="px-3 py-2.5 font-semibold">Asignatura</th>
                        <th class="px-3 py-2.5 font-semibold">Docente</th>
                        <th class="px-3 py-2.5 font-semibold">Aula / Espacio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($horarios as $h)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="whitespace-nowrap px-3 py-2">
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                    {{ $h->dia_semana }}
                                </span>
                                <div class="mt-0.5 text-[11px] font-semibold text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A') }} &ndash; {{ \Carbon\Carbon::parse($h->hora_fin)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                {{ $h->materia?->nombre ?? 'N/A' }}
                                @if($h->materia?->codigo)
                                    <span class="text-[10px] text-gray-400">({{ $h->materia->codigo }})</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300">
                                @if($h->profesor)
                                    <div class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $h->profesor->nombre }} {{ $h->profesor->apellido }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">C.I. {{ $h->profesor->cedula ?: 'N/A' }}</div>
                                @else
                                    <span class="italic text-gray-400">Por asignar</span>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($h->espacio)
                                    <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        {{ $h->espacio->nombre }}
                                    </span>
                                @else
                                    <span class="italic text-gray-400">Sin aula</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
