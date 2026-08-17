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
use Illuminate\Database\Eloquent\Builder;

class ProfesorResource extends Resource
{
    protected static ?string $model = Profesor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'nombre';
    protected static ?string $navigationLabel = 'Docentes';
    protected static ?string $pluralModelLabel = 'Docentes';
    protected static ?string $modelLabel = 'Docente';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 2;



    public static function form(Schema $schema): Schema
    {
        return ProfesorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfesorsTable::configure($table);
    }

            public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['nivelAcademico', 'especialidad']);
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
