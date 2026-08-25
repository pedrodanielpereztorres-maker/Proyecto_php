<?php

namespace App\Filament\Resources\PeriodoAcademicos;

use App\Filament\Resources\PeriodoAcademicos\Pages\CreatePeriodoAcademico;
use App\Filament\Resources\PeriodoAcademicos\Pages\EditPeriodoAcademico;
use App\Filament\Resources\PeriodoAcademicos\Pages\ListPeriodoAcademicos;
use App\Models\PeriodoAcademico;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class PeriodoAcademicoResource extends Resource
{
    protected static ?string $model = PeriodoAcademico::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $slug = 'periodos-academicos';

    protected static ?string $recordTitleAttribute = 'codigo';
    protected static ?string $navigationLabel = 'Períodos Académicos';
    protected static ?string $pluralModelLabel = 'Períodos Académicos';
    protected static ?string $modelLabel = 'Período Académico';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuración del Sistema';
    protected static ?int $navigationSort = 1;



    
    
    
    
    
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identificación y Estado del Ciclo')
                ->description('Configura la nomenclatura oficial y el estado operativo del período.')
                ->icon('heroicon-o-information-circle')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('codigo')
                        ->label('Código del Período')
                        ->placeholder('Ej: PR26-2, 2026-II...')
                        ->helperText('Identificador único oficial del ciclo lectivo.')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),

                    \Filament\Forms\Components\Select::make('estado')
                        ->label('Estado del Período')
                        ->options([
                            'planificacion' => 'En Planificación (Borrador / Preparación)',
                            'curso' => 'En Curso (Activo / Visible en Portal)',
                            'cerrado' => 'Cerrado (Histórico / Finalizado)',
                        ])
                        ->native(false)
                        ->prefixIcon('heroicon-m-flag')
                        ->helperText('Controla la fase del ciclo académico en el sistema.')
                        ->default('planificacion')
                        ->required(),
                ]),

            Section::make('Calendario y Duración Lectiva')
                ->description('Define las fechas límites del ciclo. La cantidad de semanas se calcula en automático pero puedes ajustarla manualmente.')
                ->icon('heroicon-o-calendar-days')
                ->columnSpanFull()
                ->columns(3)
                ->schema([
                    DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->placeholder('Selecciona fecha...')
                        ->prefixIcon('heroicon-m-calendar')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function (callable $get, callable $set) {
                            $inicio = $get('fecha_inicio');
                            $fin = $get('fecha_fin');
                            if ($inicio && $fin) {
                                $dias = \Carbon\Carbon::parse($inicio)->diffInDays(\Carbon\Carbon::parse($fin));
                                $semanas = max(1, (int) round($dias / 7));
                                $set('duracion_semanas', $semanas);
                            }
                        })
                        ->required(),

                    DatePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->placeholder('Selecciona fecha...')
                        ->prefixIcon('heroicon-m-calendar')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function (callable $get, callable $set) {
                            $inicio = $get('fecha_inicio');
                            $fin = $get('fecha_fin');
                            if ($inicio && $fin) {
                                $dias = \Carbon\Carbon::parse($inicio)->diffInDays(\Carbon\Carbon::parse($fin));
                                $semanas = max(1, (int) round($dias / 7));
                                $set('duracion_semanas', $semanas);
                            }
                        })
                        ->afterOrEqual('fecha_inicio')
                        ->helperText('Igual o posterior al inicio.')
                        ->required(),

                    TextInput::make('duracion_semanas')
                        ->label('Duración en Semanas')
                        ->placeholder('Ej: 16')
                        ->helperText('Cálculo automático o editable a mano.')
                        ->prefixIcon('heroicon-m-clock')
                        ->suffix('semanas')
                        ->numeric()
                        ->default(16)
                        ->minValue(1)
                        ->maxValue(52)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código Período')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-calendar-days')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-calendar')
                    ->sortable(),

                TextColumn::make('fecha_fin')
                    ->label('Fecha Fin')
                    ->date('d/m/Y')
                    ->icon('heroicon-m-calendar')
                    ->sortable(),

                TextColumn::make('duracion_semanas')
                    ->label('Duración')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => "{$state} sem.")
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'planificacion',
                        'success' => 'curso',
                        'danger' => 'cerrado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planificacion' => 'En Planificación',
                        'curso' => 'En Curso',
                        'cerrado' => 'Cerrado',
                        default => $state,
                    })
                    ->alignCenter(),

                TextColumn::make('horarios_count')
                    ->label('Clases')
                    ->counts('horarios')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('codigo', 'desc')
            ->recordActions([
                \Filament\Actions\Action::make('iniciar_curso')
                    ->label('Iniciar Período')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('¿Iniciar Período Académico?')
                    ->modalDescription('Al iniciar el período, los horarios aprobados se harán visibles en el portal público. ¿Deseas continuar?')
                    ->modalSubmitActionLabel('Sí, Iniciar')
                    ->visible(fn (PeriodoAcademico $record): bool => $record->estado === 'planificacion')
                    ->action(function (PeriodoAcademico $record) {
                        $record->update(['estado' => 'curso']);
                        \Filament\Notifications\Notification::make()
                            ->title('Período iniciado con éxito')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPeriodoAcademicos::route('/'),
            'create' => CreatePeriodoAcademico::route('/create'),
            'edit' => EditPeriodoAcademico::route('/{record}/edit'),
        ];
    }
}
