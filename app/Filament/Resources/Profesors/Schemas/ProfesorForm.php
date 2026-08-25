<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profesors\Schemas;

use App\Models\Especialidad;
use App\Models\NivelAcademico;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfesorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal y Fotografía')
                    ->description('Datos de identidad y fotografía oficial del docente.')
                    ->icon('heroicon-o-user')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Fotografía Oficial')
                            ->helperText('Foto frontal para el carnet y ficha docente.')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->alignment('center')
                            ->directory('profesores')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('cedula')
                                    ->label('Cédula de Identidad')
                                    ->placeholder('Ej: V-12345678')
                                    ->helperText('Cédula oficial con formato V o E.')
                                    ->prefixIcon('heroicon-m-identification')
                                    ->required()
                                    ->maxLength(32)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $record) {
                                        if (empty($state)) return;
                                        $cedulaLimpia = trim($state);
                                        $existente = \App\Models\Profesor::where('cedula', $cedulaLimpia)
                                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                            ->first();

                                        if ($existente) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('⚠️ Cédula ya Registrada')
                                                ->body("La cédula '{$state}' ya pertenece al profesor(a): {$existente->nombre} {$existente->apellido}.")
                                                ->warning()
                                                ->persistent()
                                                ->send();
                                        }
                                    })
                                    ->rules([
                                        fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                                            $existente = \App\Models\Profesor::where('cedula', trim($value))
                                                ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                                ->first();
                                            if ($existente) {
                                                $fail("Esta cédula ya está registrada para el profesor {$existente->nombre} {$existente->apellido}.");
                                            }
                                        },
                                    ])
                                    ->unique(ignoreRecord: true),

                                TextInput::make('codigo_interno')
                                    ->label('Código Interno')
                                    ->placeholder('Ej: DOC-001')
                                    ->helperText('Código correlativo automático o personalizable a mano.')
                                    ->prefixIcon('heroicon-m-hashtag')
                                    ->default(function () {
                                        $ultimoId = \App\Models\Profesor::max('id') ?? 0;
                                        return 'DOC-' . str_pad((string)($ultimoId + 1), 3, '0', STR_PAD_LEFT);
                                    })
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(64),

                                TextInput::make('nombre')
                                    ->label('Nombres')
                                    ->placeholder('Ej: Carlos Eduardo')
                                    ->helperText('Nombres completos del docente.')
                                    ->prefixIcon('heroicon-m-user')
                                    ->required()
                                    ->maxLength(128),

                                TextInput::make('apellido')
                                    ->label('Apellidos')
                                    ->placeholder('Ej: Pérez Gómez')
                                    ->helperText('Apellidos completos del docente.')
                                    ->prefixIcon('heroicon-m-user')
                                    ->required()
                                    ->maxLength(128),
                            ]),
                    ]),

                Section::make('Perfil Académico y Especialidad')
                    ->description('Titulación universitaria y área principal en la que dicta clases.')
                    ->icon('heroicon-o-academic-cap')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('nivel_academico_id')
                            ->label('Nivel Académico / Título')
                            ->placeholder('Selecciona nivel (Ej: Ingeniero, Licenciado, TSU)...')
                            ->helperText('Grado de instrucción universitario máximo alcanzado.')
                            ->prefixIcon('heroicon-m-academic-cap')
                            ->options(fn () => NivelAcademico::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable(),

                        Select::make('especialidad_id')
                            ->label('Especialidad / Mención')
                            ->placeholder('Selecciona especialidad (Ej: Análisis de Sistemas)...')
                            ->helperText('Área disciplinaria principal del docente.')
                            ->prefixIcon('heroicon-m-sparkles')
                            ->options(fn () => Especialidad::query()->orderBy('nombre')->pluck('nombre', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable(),
                    ]),

                Section::make('Contacto y Residencia')
                    ->description('Canales directos para envío de horarios y notificaciones institucionales.')
                    ->icon('heroicon-o-phone')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('Ej: docente@iutepi.edu.ve')
                            ->helperText('Canal para envío digital de circulares y horarios.')
                            ->prefixIcon('heroicon-m-envelope')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('telefono')
                            ->label('Teléfono / WhatsApp')
                            ->placeholder('Ej: 0414-5551234')
                            ->helperText('Número móvil para envío directo de horarios por WhatsApp.')
                            ->prefixIcon('heroicon-m-phone')
                            ->tel()
                            ->maxLength(24),

                        Textarea::make('direccion')
                            ->label('Dirección de Habitación')
                            ->placeholder('Ej: Av. Principal, Res. Los Cedros, Apto 2-B, Acarigua, Edo. Portuguesa...')
                            ->helperText('Domicilio actual del docente.')
                            ->maxLength(512)
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
