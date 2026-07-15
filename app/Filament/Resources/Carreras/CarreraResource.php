<?php

namespace App\Filament\Resources\Carreras;

use App\Filament\Resources\Carreras\Pages\CreateCarrera;
use App\Filament\Resources\Carreras\Pages\EditCarrera;
use App\Filament\Resources\Carreras\Pages\ListCarreras;
use App\Filament\Resources\Carreras\Schemas\CarreraForm;
use App\Filament\Resources\Carreras\Tables\CarrerasTable;
use App\Models\Carrera;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CarreraResource extends Resource
{
    protected static ?string $model = Carrera::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return CarreraForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CarrerasTable::configure($table);
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
            'index' => ListCarreras::route('/'),
            'create' => CreateCarrera::route('/create'),
            'edit' => EditCarrera::route('/{record}/edit'),
        ];
    }
}
