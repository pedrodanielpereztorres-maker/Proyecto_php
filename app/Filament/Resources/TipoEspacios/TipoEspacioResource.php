<?php

namespace App\Filament\Resources\TipoEspacios;

use App\Filament\Resources\TipoEspacios\Pages\CreateTipoEspacio;
use App\Filament\Resources\TipoEspacios\Pages\EditTipoEspacio;
use App\Filament\Resources\TipoEspacios\Pages\ListTipoEspacios;
use App\Filament\Resources\TipoEspacios\Schemas\TipoEspacioForm;
use App\Filament\Resources\TipoEspacios\Tables\TipoEspaciosTable;
use App\Models\TipoEspacio;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TipoEspacioResource extends Resource
{
    protected static ?string $model = TipoEspacio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?string $modelLabel = 'Tipo de Espacio';

    protected static ?string $pluralModelLabel = 'Tipos de Espacio';

    public static function form(Schema $schema): Schema
    {
        return TipoEspacioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoEspaciosTable::configure($table);
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
            'index' => ListTipoEspacios::route('/'),
            'create' => CreateTipoEspacio::route('/create'),
            'edit' => EditTipoEspacio::route('/{record}/edit'),
        ];
    }
}