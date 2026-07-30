<?php

namespace Tests\Unit;

use App\Models\Carrera;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class DepartamentoModelTest extends TestCase
{
    public function test_carrera_can_resolve_the_departamento_relationship(): void
    {
        $this->assertTrue(class_exists(Departamento::class));

        $carrera = new Carrera();

        $this->assertInstanceOf(BelongsTo::class, $carrera->departamento());
    }
}
