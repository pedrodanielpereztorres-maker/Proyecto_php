<?php

declare(strict_types=1);

namespace App\Filament\Resources\Seccions;

use App\Filament\Resources\Seccions\Pages\CreateSeccion;
use App\Filament\Resources\Seccions\Pages\EditSeccion;
use App\Filament\Resources\Seccions\Pages\ListSeccions;
use App\Filament\Resources\Seccions\Schemas\SeccionForm;
use App\Filament\Resources\Seccions\Tables\SeccionTable;
use App\Models\Seccion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SeccionResource extends Resource
{
    protected static ?string $model = Seccion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Secciones';
    protected static ?string $pluralModelLabel = 'Secciones';
    protected static ?string $modelLabel = 'Sección';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 1;



    
    
    
    public static function form(Schema $schema): Schema
    {
        return SeccionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeccionTable::configure($table);
    }

            public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['periodoAcademico', 'turno', 'carrera']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeccions::route('/'),
            'create' => CreateSeccion::route('/create'),
            'edit' => EditSeccion::route('/{record}/edit'),
        ];
    }
}
