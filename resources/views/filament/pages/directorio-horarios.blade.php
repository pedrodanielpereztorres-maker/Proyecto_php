<x-filament-panels::page>
    @php
        $stats = $this->getEstadisticas();
    @endphp

    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
        .kpi-card {
            background-color: var(--card-bg, #ffffff);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        .dark .kpi-card {
            background-color: #111827;
            border-color: rgba(55, 65, 81, 0.8);
        }
        .kpi-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>

    {{-- HEADER STATS CARDS --}}
    <div class="kpi-grid">
        {{-- CARD 1: SECCIONES ACTIVAS --}}
        <div class="kpi-card">
            <div class="kpi-icon-box" style="background-color: #eff6ff; color: #2563eb;">
                <x-filament::icon icon="heroicon-o-rectangle-group" class="h-6 w-6" />
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; line-height: 1.1; color: #1e293b;" class="dark:text-white">
                    {{ $stats['total_secciones'] }}
                </div>
                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-top: 2px;">
                    Secciones con Horario
                </div>
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #3b82f6, #6366f1);"></div>
        </div>

        {{-- CARD 2: TOTAL BLOQUES ASIGNADOS --}}
        <div class="kpi-card">
            <div class="kpi-icon-box" style="background-color: #ecfdf5; color: #059669;">
                <x-filament::icon icon="heroicon-o-calendar-days" class="h-6 w-6" />
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; line-height: 1.1; color: #1e293b;" class="dark:text-white">
                    {{ $stats['total_bloques'] }}
                </div>
                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-top: 2px;">
                    Bloques Planificados
                </div>
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #10b981, #14b8a6);"></div>
        </div>

        {{-- CARD 3: CARRERAS CON HORARIO --}}
        <div class="kpi-card">
            <div class="kpi-icon-box" style="background-color: #fffbeb; color: #d97706;">
                <x-filament::icon icon="heroicon-o-academic-cap" class="h-6 w-6" />
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 800; line-height: 1.1; color: #1e293b;" class="dark:text-white">
                    {{ $stats['total_carreras'] }}
                </div>
                <div style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin-top: 2px;">
                    Carreras Activas
                </div>
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #f59e0b, #ea580c);"></div>
        </div>
    </div>

    {{-- TABLA DE DIRECTORIO --}}
    <div class="mt-1">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
