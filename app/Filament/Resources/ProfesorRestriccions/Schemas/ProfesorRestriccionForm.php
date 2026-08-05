<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProfesorRestriccions\Schemas;

use App\Models\Profesor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProfesorRestriccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('profesor_id')
                            ->label('Profesor')
                            ->relationship('profesor', 'nombre')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Select::make('dia_semana')
                            ->label('Día de la semana')
                            ->options([
                                'Lunes' => 'Lunes',
                                'Martes' => 'Martes',
                                'Miércoles' => 'Miércoles',
                                'Jueves' => 'Jueves',
                                'Viernes' => 'Viernes',
                                'Sábado' => 'Sábado',
                            ])
                            ->required(),
                        TimePicker::make('hora_inicio')
                            ->label('Hora inicio')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('hora_fin')
                            ->label('Hora fin')
                            ->seconds(false)
                            ->required(),
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->maxLength(512)
                            ->rows(3),
                    ]),
            ]);
    }
}
