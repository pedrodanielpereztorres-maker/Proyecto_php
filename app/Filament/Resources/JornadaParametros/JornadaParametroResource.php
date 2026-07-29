<?php

namespace App\Filament\Resources\JornadaParametros;

use App\Filament\Resources\JornadaParametros\Pages\CreateJornadaParametro;
use App\Filament\Resources\JornadaParametros\Pages\EditJornadaParametro;
use App\Filament\Resources\JornadaParametros\Pages\ListJornadaParametros;
use App\Models\JornadaParametro;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class JornadaParametroResource extends Resource
{
    protected static ?string $model = JornadaParametro::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $recordTitleAttribute = 'tipo_jornada';

    protected static ?string $navigationLabel = 'Parámetros de Jornada';

    protected static ?string $pluralModelLabel = 'Parámetros de Jornada';

    protected static ?string $modelLabel = 'Parámetro de Jornada';

    protected static UnitEnum|string|null $navigationGroup = 'Configuración Global';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('tipo_jornada')
                ->label('Tipo de Jornada')
                ->options([
                    'Semana' => 'Semana (Lunes a Viernes)',
                    'Sabatino' => 'Sabatino',
                ])
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('duracion_bloque_minutos')
                ->label('Duración del Bloque (Minutos)')
                ->numeric()
                ->required()
                ->default(45)
                ->suffix('min'),

            TextInput::make('duracion_receso_minutos')
                ->label('Duración del Receso (Minutos)')
                ->numeric()
                ->required()
                ->default(15)
                ->suffix('min'),

            TimePicker::make('hora_inicio')
                ->label('Hora de Inicio')
                ->required()
                ->default('07:30'),

            TimePicker::make('hora_fin')
                ->label('Hora de Fin')
                ->required()
                ->default('21:00'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_jornada')
                    ->label('Jornada')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Semana' => 'primary',
                        'Sabatino' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('duracion_bloque_minutos')
                    ->label('Bloque Academic.')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('duracion_receso_minutos')
                    ->label('Receso')
                    ->suffix(' min')
                    ->sortable(),

                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('hora_fin')
                    ->label('Fin')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('tipo_jornada', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJornadaParametros::route('/'),
            'create' => CreateJornadaParametro::route('/create'),
            'edit' => EditJornadaParametro::route('/{record}/edit'),
        ];
    }
}
