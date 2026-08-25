<?php

namespace App\Filament\Resources\Materias\Schemas;

use App\Models\Materia;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MateriaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identificación y Ubicación en el Pénsum')
                    ->description('Código oficial, denominación de la asignatura y ubicación curricular.')
                    ->icon('heroicon-o-book-open')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('codigo')
                            ->label('Código de Asignatura')
                            ->placeholder('Ej: AS-515')
                            ->helperText('Código único según la malla curricular.')
                            ->prefixIcon('heroicon-m-key')
                            ->required()
                            ->maxLength(32)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $record) {
                                if (empty($state)) return;
                                $codigoLimpio = trim($state);
                                $existente = Materia::where('codigo', $codigoLimpio)
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->first();

                                if ($existente) {
                                    Notification::make()
                                        ->title('⚠️ Código de Materia ya Registrado')
                                        ->body("El código '{$state}' ya pertenece a la asignatura: {$existente->nombre}.")
                                        ->warning()
                                        ->persistent()
                                        ->send();
                                }
                            })
                            ->rules([
                                fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    $existente = Materia::where('codigo', trim($value))
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->first();
                                    if ($existente) {
                                        $fail("Este código ya pertenece a la materia '{$existente->nombre}'.");
                                    }
                                },
                            ])
                            ->unique(ignoreRecord: true),

                        TextInput::make('nombre')
                            ->label('Nombre de la Asignatura')
                            ->placeholder('Ej: Programación IV')
                            ->helperText('Denominación completa de la materia.')
                            ->prefixIcon('heroicon-m-academic-cap')
                            ->required()
                            ->maxLength(255),

                        Select::make('carrera_id')
                            ->label('Carrera / Programa')
                            ->placeholder('Selecciona la carrera correspondiente...')
                            ->helperText('Carrera a la cual pertenece este pénsum de estudios.')
                            ->prefixIcon('heroicon-m-building-office-2')
                            ->relationship('carrera', 'nombre')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable(),

                        Select::make('semestre')
                            ->label('Semestre / Nivel')
                            ->placeholder('Selecciona el semestre...')
                            ->helperText('Nivel semestral dentro de la carrera (1° al 6°).')
                            ->prefixIcon('heroicon-m-calendar')
                            ->options([
                                1 => '1° Semestre',
                                2 => '2° Semestre',
                                3 => '3° Semestre',
                                4 => '4° Semestre',
                                5 => '5° Semestre',
                                6 => '6° Semestre',
                            ])
                            ->default(1)
                            ->native(false)
                            ->required(),

                        Select::make('tipo_espacio_id')
                            ->label('Tipo de Espacio Requerido')
                            ->placeholder('Selecciona tipo de ambiente necesario...')
                            ->helperText('Espacio físico obligatorio para la clase (Aula Regular, Laboratorio, etc.).')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->relationship('tipoEspacio', 'nombre')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        TextInput::make('creditos')
                            ->label('Unidades de Crédito (U.C.)')
                            ->placeholder('Ej: 3')
                            ->helperText('Valor crediticio institucional de la asignatura.')
                            ->prefixIcon('heroicon-m-star')
                            ->numeric()
                            ->minValue(1)
                            ->default(3)
                            ->required(),
                    ]),

                Section::make('Carga Horaria y Distribución Semanal')
                    ->description('Desglose de horas teóricas (HT), prácticas (HP) y cálculo automático total.')
                    ->icon('heroicon-o-clock')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('horas_teoricas')
                            ->label('Horas Teóricas (HT)')
                            ->placeholder('Ej: 2')
                            ->helperText('Horas presenciales de teoría.')
                            ->prefixIcon('heroicon-m-book-open')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(2)
                            ->live(debounce: 300)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('horas_semanales', intval($get('horas_teoricas')) + intval($get('horas_practicas')));
                            }),

                        TextInput::make('horas_practicas')
                            ->label('Horas Prácticas (HP)')
                            ->placeholder('Ej: 2')
                            ->helperText('Horas de laboratorio o taller.')
                            ->prefixIcon('heroicon-m-wrench-screwdriver')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(2)
                            ->live(debounce: 300)
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('horas_semanales', intval($get('horas_teoricas')) + intval($get('horas_practicas')));
                            }),

                        TextInput::make('horas_semanales')
                            ->label('Total Horas Semanales (HT + HP)')
                            ->helperText('Calculado automáticamente en tiempo real.')
                            ->prefixIcon('heroicon-m-calculator')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->default(4)
                            ->readOnly(),
                    ]),
            ]);
    }
}
