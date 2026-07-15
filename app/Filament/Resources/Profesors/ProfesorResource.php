<?php

namespace App\Filament\Resources\Profesors;

use App\Filament\Resources\Profesors\Pages\CreateProfesor;
use App\Filament\Resources\Profesors\Pages\EditProfesor;
use App\Filament\Resources\Profesors\Pages\ListProfesors;
use App\Filament\Resources\Profesors\Schemas\ProfesorForm;
use App\Filament\Resources\Profesors\Tables\ProfesorsTable;
use App\Models\Profesor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfesorResource extends Resource
{
    protected static ?string $model = Profesor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return ProfesorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfesorsTable::configure($table);
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
            'index' => ListProfesors::route('/'),
            'create' => CreateProfesor::route('/create'),
            'edit' => EditProfesor::route('/{record}/edit'),
        ];
    }
}
