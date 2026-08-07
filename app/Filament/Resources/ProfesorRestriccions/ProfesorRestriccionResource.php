<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfesorRestriccions;

use App\Filament\Resources\ProfesorRestriccions\Pages\CreateProfesorRestriccion;
use App\Filament\Resources\ProfesorRestriccions\Pages\EditProfesorRestriccion;
use App\Filament\Resources\ProfesorRestriccions\Pages\ListProfesorRestriccions;
use App\Filament\Resources\ProfesorRestriccions\Schemas\ProfesorRestriccionForm;
use App\Filament\Resources\ProfesorRestriccions\Tables\ProfesorRestriccionTable;
use App\Models\ProfesorRestriccion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProfesorRestriccionResource extends Resource
{
    protected static ?string $model = ProfesorRestriccion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;
    protected static ?string $navigationLabel = 'Restricciones de Docentes';
    protected static ?string $pluralModelLabel = 'Restricciones de Docentes';
    protected static ?string $modelLabel = 'Restricción de Docente';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 3;



    
    
    
    public static function form(Schema $schema): Schema
    {
        return ProfesorRestriccionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfesorRestriccionTable::configure($table);
    }

            public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['profesor']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProfesorRestriccions::route('/'),
            'create' => CreateProfesorRestriccion::route('/create'),
            'edit' => EditProfesorRestriccion::route('/{record}/edit'),
        ];
    }
}
