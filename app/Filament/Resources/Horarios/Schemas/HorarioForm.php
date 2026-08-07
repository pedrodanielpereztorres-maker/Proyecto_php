<?php

namespace App\Filament\Resources\Horarios\Schemas;

use App\Models\PeriodoAcademico;
use App\Models\Seccion;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class HorarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('periodo_academico_id')
                    ->label('Período Académico')
                    ->options(fn () => PeriodoAcademico::query()->orderBy('codigo', 'desc')->pluck('codigo', 'id')->toArray())
                    ->default(fn () => PeriodoAcademico::query()->whereIn('estado', ['curso', 'planificacion'])->orderByDesc('id')->value('id'))
                    ->required(),
                Select::make('materia_id')
                    ->label('Materia')
                    ->relationship('materia', 'nombre')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('profesor_id')
                    ->label('Docente')
                    ->relationship('profesor', 'nombre')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('espacio_id')
                    ->label('Espacio')
                    ->relationship('espacio', 'codigo')
                    ->preload()
                    ->searchable()
                    ->required(),
                Select::make('seccion_id')
                    ->label('Sección')
                    ->relationship('seccion', 'codigo')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) =>
                        $set('_semestre_display', $state
                            ? (Seccion::find($state)?->semestre ?? '—')
                            : '—'
                        )
                    ),
                TextInput::make('_semestre_display')
                    ->label('Semestre de la Sección')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Se completa automáticamente al seleccionar la sección.')
                    ->prefixIcon('heroicon-m-academic-cap')
                    ->placeholder('Seleccione una sección...'),
                Toggle::make('omitir_validacion_capacidad')
                    ->label('Omitir validación de capacidad')
                    ->default(false),
                Select::make('dia_semana')
                    ->label('Día de la Semana')
                    ->options([
                        'Lunes'     => 'Lunes',
                        'Martes'    => 'Martes',
                        'Miércoles' => 'Miércoles',
                        'Jueves'    => 'Jueves',
                        'Viernes'   => 'Viernes',
                        'Sábado'    => 'Sábado',
                    ])
                    ->required(),
                TimePicker::make('hora_inicio')
                    ->label('Hora de Inicio')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('hora_fin')
                    ->label('Hora de Fin')
                    ->seconds(false)
                    ->required(),
            ]);
    }
}
