<?php

namespace Tests\Feature;

use App\Models\PeriodoAcademico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodoAcademicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_periodo_academico_with_code_and_dates(): void
    {
        $periodo = PeriodoAcademico::create([
            'codigo' => 'PR26-2',
            'fecha_inicio' => '2026-08-01',
            'fecha_fin' => '2026-12-15',
            'activo' => true,
        ]);

        $this->assertDatabaseHas('periodos_academicos', [
            'id' => $periodo->id,
            'codigo' => 'PR26-2',
            'activo' => true,
        ]);
    }
}
