<div class="max-w-6xl mx-auto p-6 bg-white min-h-screen">
    <style>
        :root {
            --primary-color: {{ $colorPrincipal }};
            --primary-hover: color-mix(in srgb, {{ $colorPrincipal }} 80%, black);
            --primary-light: color-mix(in srgb, {{ $colorPrincipal }} 10%, white);
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .text-primary-custom {
            color: var(--primary-color);
        }
        .border-primary-custom {
            border-color: var(--primary-color);
        }
        .focus-primary:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-light);
        }
    </style>

    <div class="text-center mb-8">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo {{ $nombreInst }}" class="h-20 mx-auto mb-4 object-contain">
        @endif
        <h1 class="text-3xl font-bold text-gray-800">Consulta de Horarios {{ $nombreInst }}</h1>
        <p class="text-gray-500 mt-2">Selecciona tu perfil para ver tu horario académico</p>
    </div>

    <!-- Selección de Modo -->
    @if(!$modo)
        <div class="flex flex-col sm:flex-row justify-center gap-6 mt-10">
            <button wire:click="setModo('estudiante')" class="px-8 py-4 btn-primary rounded-lg shadow text-lg font-semibold w-full sm:w-64 flex flex-col items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
                Soy Estudiante
            </button>
            <button wire:click="setModo('docente')" class="px-8 py-4 bg-gray-800 hover:bg-gray-900 text-white rounded-lg shadow text-lg font-semibold w-full sm:w-64 transition-all hover:-translate-y-0.5 hover:shadow-lg flex flex-col items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Soy Docente
            </button>
        </div>
    @endif

    <!-- Flujo Estudiante -->
    @if($modo === 'estudiante')
        <div class="mb-6">
            <button wire:click="setModo(null)" class="text-primary-custom hover:underline mb-4 inline-flex items-center gap-1 font-medium"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Volver al inicio</button>
            
            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm flex flex-wrap gap-4 items-end">
                <div class="w-full md:w-auto flex-1">
                    <label class="block text-sm font-medium text-gray-700">Período Académico</label>
                    <select wire:model.live="periodo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus-primary p-2 border">
                        <option value="">Seleccione...</option>
                        @foreach($periodos as $p)
                            <option value="{{ $p->id }}">{{ $p->codigo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-auto flex-1">
                    <label class="block text-sm font-medium text-gray-700">Carrera</label>
                    <select wire:model.live="carrera_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus-primary p-2 border" {{ empty($periodo_id) ? 'disabled' : '' }}>
                        <option value="">Seleccione...</option>
                        @foreach($carreras as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-auto flex-1">
                    <label class="block text-sm font-medium text-gray-700">Semestre</label>
                    <select wire:model.live="semestre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus-primary p-2 border" {{ empty($periodo_id) ? 'disabled' : '' }}>
                        <option value="">Seleccione...</option>
                        @for($i=1; $i<=6; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="w-full md:w-auto flex-1">
                    <label class="block text-sm font-medium text-gray-700">Sección</label>
                    <select wire:model.live="seccion_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus-primary p-2 border" {{ empty($secciones) ? 'disabled' : '' }}>
                        <option value="">Seleccione...</option>
                        @foreach($secciones as $s)
                            <option value="{{ $s->id }}">{{ $s->codigo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    <!-- Flujo Docente -->
    @if($modo === 'docente')
        <div class="mb-6">
            <button wire:click="setModo(null)" class="text-blue-500 hover:underline mb-4 inline-block">&larr; Volver al inicio</button>
            
            <div class="bg-gray-50 p-6 rounded-lg border max-w-lg mx-auto">
                <label class="block text-sm font-medium text-gray-700">Ingrese su Cédula de Identidad</label>
                <div class="mt-2 flex gap-2">
                    <input type="text" wire:model="cedula" placeholder="Ej: V-12345678" class="block w-full rounded-md border-gray-300 shadow-sm focus-primary p-2 border">
                    <button wire:click="buscarDocente" class="px-4 py-2 btn-primary rounded shadow-sm font-medium">Buscar</button>
                </div>
                @if (session()->has('error_cedula'))
                    <span class="text-red-500 text-sm mt-2 block">{{ session('error_cedula') }}</span>
                @endif
                @if ($profesor_encontrado)
                    <span class="text-green-600 text-sm mt-2 block font-semibold">¡Hola, {{ $profesor_encontrado->nombre }} {{ $profesor_encontrado->apellido }}!</span>
                @endif
            </div>
        </div>
    @endif

    <!-- Cuadrícula del Horario -->
    @if(count($horariosAsignados) > 0)
        <div class="mt-8 overflow-x-auto">
            <h2 class="text-xl font-bold mb-4">Tu Horario:</h2>
            <table class="min-w-full border-collapse border border-gray-300 text-sm text-center">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 p-2">Hora</th>
                        @foreach($dias as $dia)
                            <th class="border border-gray-300 p-2">{{ $dia }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($bloques as $bloque)
                        <tr>
                            <td class="border border-gray-300 p-2 font-semibold bg-gray-50">
                                {{ $bloque['inicio_ampm'] }} - {{ $bloque['fin_ampm'] }}
                            </td>
                            @foreach($dias as $dia)
                                @php
                                    $key = $dia . '_' . $bloque['inicio'];
                                    $asignacion = $horariosAsignados[$key] ?? null;
                                @endphp
                                
                                @if($asignacion && $asignacion->es_receso)
                                    <td class="border border-gray-300 p-2 bg-yellow-100 text-yellow-800 font-bold">RECESO</td>
                                @else
                                    <td class="border border-gray-300 p-2 {{ $asignacion ? 'bg-blue-50' : 'bg-white' }}">
                                        @if($asignacion)
                                            <div class="font-bold text-gray-800">{{ $asignacion->materia->nombre ?? 'Bloque' }}</div>
                                            @if($modo === 'estudiante')
                                                <div class="text-gray-600 text-xs">Prof: {{ $asignacion->profesor->nombre ?? '' }}</div>
                                            @else
                                                <div class="text-gray-600 text-xs">Sec: {{ $asignacion->seccion->codigo ?? '' }}</div>
                                            @endif
                                            <div class="text-gray-500 text-xs">Aula: {{ $asignacion->espacio->codigo ?? '' }}</div>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
