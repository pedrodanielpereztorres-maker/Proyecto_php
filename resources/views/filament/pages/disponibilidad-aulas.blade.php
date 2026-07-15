<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
    </form>

    @if($this->aula_id)
        <div class="mt-8 space-y-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <x-heroicon-o-building-office-2 class="w-6 h-6" />
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Disponibilidad y Uso del Aula
                </h2>
            </div>
            
            @if($this->getHorariosProperty()->isEmpty())
                <div class="flex flex-col items-center justify-center p-8 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl">
                    <x-heroicon-o-check-circle class="w-12 h-12 text-emerald-500 mb-4" />
                    <p class="text-lg font-medium text-gray-900 dark:text-white">Aula 100% Disponible</p>
                    <p class="text-gray-500 dark:text-gray-400">Esta aula no tiene asignaciones de horario registradas.</p>
                </div>
            @else
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-950 dark:text-white">Día</th>
                                <th class="px-6 py-4 font-semibold text-gray-950 dark:text-white">Horario</th>
                                <th class="px-6 py-4 font-semibold text-gray-950 dark:text-white">Materia</th>
                                <th class="px-6 py-4 font-semibold text-gray-950 dark:text-white">Profesor</th>
                                <th class="px-6 py-4 font-semibold text-gray-950 dark:text-white text-right">Carrera</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach($this->getHorariosProperty() as $horario)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            {{ $horario->dia_semana }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-200 flex items-center space-x-2">
                                        <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                                        <span>{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $horario->materia->nombre ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">
                                                {{ substr($horario->profesor->nombre ?? 'A', 0, 1) }}
                                            </div>
                                            <span>{{ $horario->profesor->nombre ?? 'N/A' }} {{ $horario->profesor->apellido ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400 text-sm">
                                        {{ $horario->materia->carrera->nombre ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
