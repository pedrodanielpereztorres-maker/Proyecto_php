<?php

namespace App\Filament\Pages;

use App\Models\Carrera;
use App\Models\Horario;
use App\Models\PeriodoAcademico;
use App\Models\Seccion;
use App\Models\Turno;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DirectorioHorarios extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Horarios Terminados';
    protected static ?string $title = 'Directorio de Horarios';
    protected static string|\UnitEnum|null $navigationGroup = 'Gestión Académica';
    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.directorio-horarios';

    public function getEstadisticas(): array
    {
        $seccionesConHorario = Seccion::query()->whereHas('horarios')->with('horarios')->get();
        
        $totalSecciones = $seccionesConHorario->count();
        $totalAlumnos = $seccionesConHorario->sum('cantidad_alumnos');
        $totalCarreras = $seccionesConHorario->pluck('carrera_id')->unique()->count();
        $totalBloques = Horario::query()->whereNotNull('seccion_id')->count();

        return [
            'total_secciones' => $totalSecciones,
            'total_alumnos' => $totalAlumnos,
            'total_carreras' => $totalCarreras,
            'total_bloques' => $totalBloques,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Seccion::query()
                    ->whereHas('horarios')
                    ->with(['carrera', 'periodoAcademico', 'turno'])
                    ->withCount('horarios')
            )
            ->columns([
                TextColumn::make('codigo')
                    ->label('Sección')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->icon('heroicon-m-rectangle-group')
                    ->description(fn (Seccion $r) => $r->cantidad_alumnos ? "{$r->cantidad_alumnos} Alumnos" : 'Sin límite'),

                TextColumn::make('periodoAcademico.codigo')
                    ->label('Período')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('carrera.nombre')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('turno.nombre')
                    ->label('Turno')
                    ->badge()
                    ->color(fn ($state) => match (strtolower((string) $state)) {
                        'matutino', 'mañana' => 'warning',
                        'vespertino', 'tarde' => 'primary',
                        'nocturno', 'noche' => 'danger',
                        'sabatino' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match (strtolower((string) $state)) {
                        'matutino', 'mañana' => 'heroicon-m-sun',
                        'vespertino', 'tarde' => 'heroicon-m-cloud',
                        'nocturno', 'noche' => 'heroicon-m-moon',
                        default => 'heroicon-m-clock',
                    })
                    ->placeholder('N/A')
                    ->sortable(),

                TextColumn::make('semestre')
                    ->label('Semestre')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}° Sem" : 'N/A')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('horarios_count')
                    ->label('Carga Asignada')
                    ->formatStateUsing(fn ($state) => "{$state} bloques")
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-check-circle')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('periodo_academico_id')
                    ->label('Período Académico')
                    ->relationship('periodoAcademico', 'codigo')
                    ->native(false)
                    ->preload(),

                SelectFilter::make('carrera_id')
                    ->label('Carrera')
                    ->relationship('carrera', 'nombre')
                    ->native(false)
                    ->preload(),

                SelectFilter::make('turno_id')
                    ->label('Turno')
                    ->relationship('turno', 'nombre')
                    ->native(false)
                    ->preload(),

                SelectFilter::make('semestre')
                    ->label('Semestre')
                    ->options([
                        '1' => '1° Semestre',
                        '2' => '2° Semestre',
                        '3' => '3° Semestre',
                        '4' => '4° Semestre',
                        '5' => '5° Semestre',
                        '6' => '6° Semestre',
                    ])
                    ->native(false),
            ])
            ->actions([
                Action::make('verHorario')
                    ->label('Generador')
                    ->tooltip('Abrir y editar en la matriz interactiva del generador')
                    ->icon('heroicon-m-pencil-square')
                    ->color('info')
                    ->url(fn (Seccion $record): string => route('filament.admin.pages.generador-horarios', [
                        'periodo_academico_id' => $record->periodo_academico_id,
                        'carrera_id' => $record->carrera_id,
                        'semestre' => $record->semestre,
                        'seccion_id' => $record->id,
                    ])),

                Action::make('descargarPdf')
                    ->label('PDF Oficial')
                    ->tooltip('Visualizar e imprimir horario institucional')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('danger')
                    ->url(fn (Seccion $record): string => route('horarios.pdf', ['seccion_id' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('compartirWhatsapp')
                    ->label('WhatsApp')
                    ->tooltip('Compartir horario por WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->action(function (Seccion $record) {
                        $horarios = Horario::query()
                            ->where('seccion_id', $record->id)
                            ->with(['materia', 'profesor', 'espacio'])
                            ->orderByRaw("FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado')")
                            ->orderBy('hora_inicio')
                            ->get();

                        $periodo = $record->periodoAcademico?->codigo ?? 'Actual';
                        $carrera = $record->carrera?->nombre ?? 'N/A';
                        $semestre = $record->semestre ? "{$record->semestre}° Semestre" : '';

                        $texto = "📅 *HORARIO DE CLASES — IUTEPI*\n";
                        $texto .= "🎓 *Carrera:* {$carrera}\n";
                        $texto .= "👥 *Sección:* {$record->codigo} ({$semestre})\n";
                        $texto .= "🗓️ *Período:* {$periodo}\n\n";
                        $texto .= "📖 *DISTRIBUCIÓN DE CLASES:*\n";
                        $texto .= "────────────────────\n";

                        foreach ($horarios as $h) {
                            $materia = $h->materia?->nombre ?? 'Materia';
                            $docente = $h->profesor ? "{$h->profesor->nombre} {$h->profesor->apellido}" : 'Por asignar';
                            $aula = $h->espacio?->codigo ?? 'Por asignar';
                            $ini = \Carbon\Carbon::parse($h->hora_inicio)->format('h:i A');
                            $fin = \Carbon\Carbon::parse($h->hora_fin)->format('h:i A');

                            $texto .= "🔹 *{$materia}*\n";
                            $texto .= "   ⏰ {$h->dia_semana}: {$ini} a {$fin}\n";
                            $texto .= "   📍 Aula: {$aula} | 👨‍🏫 Docente: {$docente}\n\n";
                        }

                        $urlPdf = route('horarios.pdf', ['seccion_id' => $record->id]);
                        $texto .= "📄 *Descarga directa en PDF:* {$urlPdf}\n";
                        $texto .= "────────────────────\n";
                        $texto .= "_Instituto Universitario de Tecnología para la Informática (IUTEPI)_";

                        $url = 'https://api.whatsapp.com/send?text=' . rawurlencode($texto);
                        $this->js("window.open('{$url}', '_blank')");
                    }),
            ])
            ->emptyStateHeading('No hay horarios terminados')
            ->emptyStateDescription('Las secciones aparecerán aquí automáticamente una vez que se les asigne al menos un bloque de clases en el Generador.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->emptyStateActions([
                Action::make('irAlGenerador')
                    ->label('Ir al Generador de Horarios')
                    ->icon('heroicon-m-sparkles')
                    ->color('primary')
                    ->url(fn (): string => route('filament.admin.pages.generador-horarios')),
            ]);
    }
}
