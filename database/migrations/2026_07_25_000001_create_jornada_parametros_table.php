<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jornada_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_jornada')->unique(); // 'Semana' o 'Sabatino'
            $table->unsignedInteger('duracion_bloque_minutos')->default(45);
            $table->unsignedInteger('duracion_receso_minutos')->default(15);
            $table->time('hora_inicio')->default('07:30:00');
            $table->time('hora_fin')->default('21:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jornada_parametros');
    }
};
