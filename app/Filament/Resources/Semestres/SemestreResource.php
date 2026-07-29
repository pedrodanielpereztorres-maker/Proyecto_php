<?php

namespace App\Filament\Resources\Semestres;

use App\Filament\Resources\Semestres\Pages\CreateSemestre;
use App\Filament\Resources\Semestres\Pages\EditSemestre;
use App\Filament\Resources\Semestres\Pages\ListSemestres;
use App\Models\Semestre;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SemestreResource extends Resource
{
    protected static ?string $model = Semestre::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?string $navigationLabel = 'Semestres';

    protected static ?string $pluralModelLabel = 'Semestres';

    protected static ?string $modelLabel = 'Semestre';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre del Semestre')
                ->placeholder('Ej: 2025-I, 2025-II')
                ->required()
                ->maxLength(100),
            Toggle::make('activo')
                ->label('Semestre Activo')
                ->helperText('Solo un semestre debe estar activo a la vez.')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Semestre')
                    ->searchable()
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
            ->defaultSort('nombre', 'desc');
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
