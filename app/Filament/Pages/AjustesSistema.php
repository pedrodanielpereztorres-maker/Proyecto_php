<?php

namespace App\Filament\Pages;

use App\Models\Configuracion;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class AjustesSistema extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static \UnitEnum|string|null $navigationGroup = 'Configuración Global';

    protected static ?string $title = 'Identidad del Sistema';

    protected static ?string $navigationLabel = 'Ajustes del Sistema';

    protected string $view = 'filament.pages.ajustes-sistema';

    public ?array $data = [];

    public function mount(): void
    {
        $config = Configuracion::first();
        if ($config) {
            $this->form->fill($config->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Información General')
                    ->description('Configura el nombre y siglas de la institución.')
                    ->icon('heroicon-o-building-library')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la Institución')
                            ->placeholder('Ej: Instituto Universitario...')
                            ->prefixIcon('heroicon-m-building-office-2')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('siglas')
                            ->label('Siglas')
                            ->placeholder('Ej: IUTEPI')
                            ->prefixIcon('heroicon-m-tag')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Ej: Av. Principal...')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->columnSpanFull()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Apariencia y Marca')
                    ->description('Sube los logos, define la URL alternativa y el color principal.')
                    ->icon('heroicon-o-swatch')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Principal (Archivo)')
                            ->helperText('Sube un archivo de imagen local.')
                            ->image()
                            ->directory('identidad'),
                        TextInput::make('logo_url')
                            ->label('URL del Logo (Alternativo)')
                            ->placeholder('https://...')
                            ->prefixIcon('heroicon-m-link')
                            ->helperText('Prioridad sobre el archivo si se especifica.')
                            ->url()
                            ->maxLength(255),
                        FileUpload::make('favicon')
                            ->label('Favicon (Icono de pestaña)')
                            ->helperText('Icono pequeño para el navegador.')
                            ->image()
                            ->directory('identidad'),
                        ColorPicker::make('color_principal')
                            ->label('Color Principal del Sistema')
                            ->helperText('Define el color base de la interfaz.')
                            ->required(),
                        ColorPicker::make('color_secundario')
                            ->label('Color Secundario')
                            ->helperText('Define el color complementario (para detalles y PDFs).')
                            ->default('#ffffff'),
                    ])->columns(2),

                Section::make('Contacto y Reportes (PDFs)')
                    ->description('Configura los datos de contacto y el formato de los reportes/horarios en PDF.')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('email_contacto')
                            ->label('Email de Contacto')
                            ->email()
                            ->prefixIcon('heroicon-m-envelope')
                            ->maxLength(255),
                        TextInput::make('telefono_contacto')
                            ->label('Teléfono de Contacto')
                            ->prefixIcon('heroicon-m-phone')
                            ->maxLength(255),
                        TextInput::make('pie_pagina_pdf')
                            ->label('Texto para Pie de Página (PDF)')
                            ->placeholder('Ej: Av. Principal, Edificio X, Nro 10...')
                            ->prefixIcon('heroicon-m-document-text')
                            ->columnSpanFull()
                            ->maxLength(255),
                        TextInput::make('director_academico')
                            ->label('Director Académico')
                            ->helperText('Nombre completo para firmas.')
                            ->prefixIcon('heroicon-m-user-circle')
                            ->maxLength(255),
                        TextInput::make('coordinador_general')
                            ->label('Coordinador General')
                            ->helperText('Nombre completo para firmas.')
                            ->prefixIcon('heroicon-m-user-circle')
                            ->maxLength(255),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Ajustes')
                ->action('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $config = Configuracion::first();
            if ($config) {
                $config->update($data);
            } else {
                Configuracion::create($data);
            }

            Notification::make()
                ->success()
                ->title('Ajustes guardados')
                ->body('La configuración se ha actualizado correctamente. Recarga la página para ver los cambios de color/logo.')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }
}
