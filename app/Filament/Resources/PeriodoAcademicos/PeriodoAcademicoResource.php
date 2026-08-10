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
            Section::make('Configuración del Período')
                ->description('Establece los parámetros y fechas de este ciclo académico.')
                ->icon('heroicon-o-calendar')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('codigo')
                        ->label('Código del Período')
                        ->placeholder('Ej: PR26-2')
                        ->helperText('Identificador único oficial.')
                        ->prefixIcon('heroicon-m-hashtag')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                        
                    \Filament\Forms\Components\Select::make('estado')
                        ->label('Estado del Período')
                        ->options([
                            'planificacion' => 'En Planificación',
                            'curso' => 'En Curso',
                            'cerrado' => 'Cerrado',
                        ])
                        ->native(false)
                        ->prefixIcon('heroicon-m-flag')
                        ->default('planificacion')
                        ->required(),

                    DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required()
                        ->native(false),

                    DatePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->required()
                        ->afterOrEqual('fecha_inicio')
                        ->native(false)
                        ->helperText('Debe ser igual o posterior a la fecha de inicio.'),

                    TextInput::make('duracion_semanas')
                        ->label('Duración aproximada')
                        ->suffix('semanas')
                        ->numeric()
                        ->default(16)
                        ->minValue(1)
                        ->maxValue(52)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código Período')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('fecha_fin')
                    ->label('Fecha Fin')
                    ->date('d/m/Y')
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
                    }),

                TextColumn::make('duracion_semanas')
                    ->label('Semanas')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('horarios_count')
                    ->label('Horarios')
                    ->counts('horarios')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
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
