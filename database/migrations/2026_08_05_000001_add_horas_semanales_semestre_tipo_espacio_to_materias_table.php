<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->integer('horas_semanales')->default(2);
            $table->integer('semestre')->default(1);
            $table->foreignId('tipo_espacio_id')->nullable()->constrained('tipo_espacios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_espacio_id');
            $table->dropColumn(['horas_semanales', 'semestre']);
        });
    }
};
