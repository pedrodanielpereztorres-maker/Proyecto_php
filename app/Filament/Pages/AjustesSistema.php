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
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la Institución')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('siglas')
                            ->label('Siglas')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Section::make('Apariencia y Marca')
                    ->description('Sube los logos y define el color principal.')
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo Principal')
                            ->image()
                            ->directory('identidad'),
                        FileUpload::make('favicon')
                            ->label('Favicon (Icono de pestaña)')
                            ->image()
                            ->directory('identidad'),
                        ColorPicker::make('color_principal')
                            ->label('Color Principal del Sistema')
                            ->required(),
                    ])->columns(3),
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
