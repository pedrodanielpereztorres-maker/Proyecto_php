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

    protected static ?string $recordTitleAttribute = 'codigo';

    protected static ?string $navigationLabel = 'Periodos Académicos';

    protected static ?string $pluralModelLabel = 'Periodos Académicos';

    protected static ?string $modelLabel = 'Periodo Académico';

    protected static UnitEnum|string|null $navigationGroup = 'Configuración Global';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')
                ->label('Código del Período')
                ->placeholder('Ej: PR26-2')
                ->required()
                ->maxLength(100)
                ->unique(ignoreRecord: true),

            DatePicker::make('fecha_inicio')
                ->label('Fecha de Inicio')
                ->native(false),

            DatePicker::make('fecha_fin')
                ->label('Fecha de Fin')
                ->native(false),

            Toggle::make('activo')
                ->label('Período Activo')
                ->helperText('Marca este período como el activo institucionalmente.')
                ->default(false),
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

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean(),

                TextColumn::make('horarios_count')
                    ->label('Horarios')
                    ->counts('horarios')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('codigo', 'desc');
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
