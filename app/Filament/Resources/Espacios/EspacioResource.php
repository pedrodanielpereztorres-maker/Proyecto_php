<?php

namespace App\Filament\Resources\Espacios;

use App\Filament\Resources\Espacios\Pages\CreateEspacio;
use App\Filament\Resources\Espacios\Pages\EditEspacio;
use App\Filament\Resources\Espacios\Pages\ListEspacios;
use App\Filament\Resources\Espacios\Schemas\EspacioForm;
use App\Filament\Resources\Espacios\Tables\EspaciosTable;
use App\Models\Espacio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EspacioResource extends Resource
{
    protected static ?string $model = Espacio::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $recordTitleAttribute = 'codigo';
    protected static ?string $navigationLabel = 'Aulas y Espacios';
    protected static ?string $pluralModelLabel = 'Aulas y Espacios';
    protected static ?string $modelLabel = 'Espacio';
    protected static string|\UnitEnum|null $navigationGroup = 'Infraestructura';
    protected static ?int $navigationSort = 1;


    public static function form(Schema $schema): Schema
    {
        return EspacioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EspaciosTable::configure($table);
    }

        public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['tipoEspacio']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEspacios::route('/'),
            'create' => CreateEspacio::route('/create'),
            'edit' => EditEspacio::route('/{record}/edit'),
        ];
    }
}
