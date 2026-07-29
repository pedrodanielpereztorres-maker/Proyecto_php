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
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->default('Instituto Universitario de Tecnología Para la Informática');
            $table->string('siglas')->default('IUTEPI');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('color_principal')->default('#c71b04'); // Rojo por defecto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};
