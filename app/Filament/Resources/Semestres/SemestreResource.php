<?php

namespace App\Filament\Resources\Semestres;

use App\Filament\Resources\Semestres\Pages\CreateSemestre;
use App\Filament\Resources\Semestres\Pages\EditSemestre;
use App\Filament\Resources\Semestres\Pages\ListSemestres;
use App\Models\PeriodoAcademico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SemestreResource extends Resource
{
    protected static ?string $model = PeriodoAcademico::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'codigo';

    protected static ?string $navigationLabel = 'Periodos Académicos';

    protected static ?string $pluralModelLabel = 'Periodos Académicos';

    protected static ?string $modelLabel = 'Periodo Académico';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('codigo')
                ->label('Código del Periodo')
                ->placeholder('Ej: PR26-2')
                ->required()
                ->maxLength(100),
            DatePicker::make('fecha_inicio')
                ->label('Fecha de Inicio')
                ->required(),
            DatePicker::make('fecha_fin')
                ->label('Fecha de Fin')
                ->required(),
            Toggle::make('activo')
                ->label('Periodo Activo')
                ->helperText('Solo un periodo debe estar activo a la vez.')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('Periodo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
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
            'index'  => ListSemestres::route('/'),
            'create' => CreateSemestre::route('/create'),
            'edit'   => EditSemestre::route('/{record}/edit'),
        ];
    }
}
