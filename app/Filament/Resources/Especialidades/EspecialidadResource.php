<?php

declare(strict_types=1);

namespace App\Filament\Resources\Especialidades;

use App\Filament\Resources\Especialidades\Pages\CreateEspecialidad;
use App\Filament\Resources\Especialidades\Pages\EditEspecialidad;
use App\Filament\Resources\Especialidades\Pages\ListEspecialidades;
use App\Filament\Resources\Especialidades\Schemas\EspecialidadForm;
use App\Filament\Resources\Especialidades\Tables\EspecialidadesTable;
use App\Models\Especialidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EspecialidadResource extends Resource
{
    protected static ?string $model = Especialidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    
    
    
    protected static ?string $recordTitleAttribute = 'nombre';
    protected static ?string $navigationLabel = 'Especialidades';
    protected static ?string $pluralModelLabel = 'Especialidades';
    protected static ?string $modelLabel = 'Especialidad';
    protected static string|\UnitEnum|null $navigationGroup = 'Configuración del Sistema';
    protected static ?int $navigationSort = 3;



    protected static ?string $slug = 'especialidades';
    public static function form(Schema $schema): Schema
    {
        return EspecialidadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EspecialidadesTable::configure($table);
    }

        public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['carrera']);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEspecialidades::route('/'),
            'create' => CreateEspecialidad::route('/create'),
            'edit' => EditEspecialidad::route('/{record}/edit'),
        ];
    }
}
