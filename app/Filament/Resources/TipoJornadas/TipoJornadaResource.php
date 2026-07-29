<?php

namespace App\Filament\Resources\TipoJornadas;

use App\Filament\Resources\TipoJornadas\Pages\CreateTipoJornada;
use App\Filament\Resources\TipoJornadas\Pages\EditTipoJornada;
use App\Filament\Resources\TipoJornadas\Pages\ListTipoJornadas;
use App\Filament\Resources\TipoJornadas\Schemas\TipoJornadaForm;
use App\Filament\Resources\TipoJornadas\Tables\TipoJornadasTable;
use App\Models\TipoJornada;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TipoJornadaResource extends Resource
{
    protected static ?string $model = TipoJornada::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Tipos de Jornada';

    protected static ?string $modelLabel = 'Tipo de Jornada';

    protected static \UnitEnum|string|null $navigationGroup = 'Configuración Global';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return TipoJornadaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TipoJornadasTable::configure($table);
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
            'index' => ListTipoJornadas::route('/'),
            'create' => CreateTipoJornada::route('/create'),
            'edit' => EditTipoJornada::route('/{record}/edit'),
        ];
    }
}
