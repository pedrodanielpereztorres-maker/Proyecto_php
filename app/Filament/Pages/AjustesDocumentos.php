<?php

namespace App\Filament\Pages;

use App\Models\ConfiguracionDocumento;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class AjustesDocumentos extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static \UnitEnum|string|null $navigationGroup = 'Configuración Global';

    protected static ?string $title = 'Membretes y Documentos';

    protected static ?string $navigationLabel = 'Ajustes de Documentos';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.ajustes-documentos';

    public ?array $data = [];

    public function mount(): void
    {
        $config = ConfiguracionDocumento::first();
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
                Section::make('Membretes Globales (PDF)')
                    ->description('Configura las imágenes que aparecerán como membrete principal y pie de página en los PDF (Horarios, Reportes, etc.).')
                    ->icon('heroicon-o-photo')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('membrete_encabezado')
                            ->label('Membrete de Encabezado (Top)')
                            ->helperText('Imagen ancha (ej: 1200x200px) que va en la parte superior del documento.')
                            ->image()
                            ->directory('documentos'),
                        FileUpload::make('membrete_pie')
                            ->label('Membrete de Pie de Página (Bottom)')
                            ->helperText('Imagen ancha que va al final del documento (opcional).')
                            ->image()
                            ->directory('documentos'),
                    ])->columns(2),

                Section::make('Fondo Institucional')
                    ->description('Opcional. Una imagen semitransparente que se colocará en el centro de las hojas.')
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('marca_de_agua')
                            ->label('Marca de Agua (Watermark)')
                            ->helperText('Se recomienda un logo central con baja opacidad.')
                            ->image()
                            ->directory('documentos'),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Documentos')
                ->action('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            $config = ConfiguracionDocumento::first();
            if ($config) {
                $config->update($data);
            } else {
                ConfiguracionDocumento::create($data);
            }

            Notification::make()
                ->success()
                ->title('Ajustes guardados')
                ->body('La configuración de documentos se ha actualizado correctamente.')
                ->send();
        } catch (Halt $exception) {
            return;
        }
    }
}
