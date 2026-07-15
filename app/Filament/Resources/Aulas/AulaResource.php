<?php

namespace App\Filament\Resources\Aulas;

use App\Filament\Resources\Aulas\Pages\CreateAula;
use App\Filament\Resources\Aulas\Pages\EditAula;
use App\Filament\Resources\Aulas\Pages\ListAulas;
use App\Filament\Resources\Aulas\Schemas\AulaForm;
use App\Filament\Resources\Aulas\Tables\AulasTable;
use App\Models\Aula;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AulaResource extends Resource
{
    protected static ?string $model = Aula::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function form(Schema $schema): Schema
    {
        return AulaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AulasTable::configure($table);
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
            'index' => ListAulas::route('/'),
            'create' => CreateAula::route('/create'),
            'edit' => EditAula::route('/{record}/edit'),
        ];
    }
}
