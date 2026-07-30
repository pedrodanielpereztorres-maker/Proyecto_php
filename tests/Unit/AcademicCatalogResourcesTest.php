<?php

namespace Tests\Unit;

use App\Models\NivelAcademico;
use Tests\TestCase;

class AcademicCatalogResourcesTest extends TestCase
{
    public function test_academic_catalog_resources_are_available_in_filament(): void
    {
        $this->assertTrue(class_exists(\App\Filament\Resources\NivelesAcademicos\NivelAcademicoResource::class));
        $this->assertTrue(class_exists(\App\Filament\Resources\Especialidades\EspecialidadResource::class));
    }

    public function test_nivel_academico_model_uses_the_expected_database_table(): void
    {
        $this->assertSame('niveles_academicos', (new NivelAcademico())->getTable());
    }
}
