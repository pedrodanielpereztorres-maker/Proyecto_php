<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('secciones')) {
            Schema::create('secciones', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->cascadeOnDelete();
                $table->foreignId('turno_id')->constrained('turnos')->cascadeOnDelete();
                $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
                $table->string('semestre');
                $table->string('codigo')->unique();
                $table->integer('cantidad_alumnos')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
