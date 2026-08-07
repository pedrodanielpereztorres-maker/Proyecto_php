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
        Schema::create('configuracion_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('membrete_encabezado')->nullable();
            $table->string('membrete_pie')->nullable();
            $table->string('marca_de_agua')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_documentos');
    }
};
