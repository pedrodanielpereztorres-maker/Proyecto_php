<?php

declare(strict_types=1);

namespace App\Filament\Resources\NivelesAcademicos;

use App\Filament\Resources\NivelesAcademicos\Pages\CreateNivelAcademico;
use App\Filament\Resources\NivelesAcademicos\Pages\EditNivelAcademico;
use App\Filament\Resources\NivelesAcademicos\Pages\ListNivelesAcademicos;
use App\Filament\Resources\NivelesAcademicos\Schemas\NivelAcademicoForm;
use App\Filament\Resources\NivelesAcademicos\Tables\NivelesAcademicosTable;
use App\Models\NivelAcademico;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NivelAcademicoResource extends Resource
{
    protected static ?string $model = NivelAcademico::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $pluralModelLabel = 'Niveles Académicos';

    protected static ?string $modelLabel = 'Nivel Académico';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?string $slug = 'niveles-academicos';
    public static function form(Schema $schema): Schema
    {
        return NivelAcademicoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NivelesAcademicosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNivelesAcademicos::route('/'),
            'create' => CreateNivelAcademico::route('/create'),
            'edit' => EditNivelAcademico::route('/{record}/edit'),
        ];
    }
}
