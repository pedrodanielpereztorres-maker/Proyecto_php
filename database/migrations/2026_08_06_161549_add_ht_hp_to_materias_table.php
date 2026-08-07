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
        Schema::table('materias', function (Blueprint $table) {
            $table->integer('horas_teoricas')->default(0)->after('horas_semanales');
            $table->integer('horas_practicas')->default(0)->after('horas_teoricas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropColumn(['horas_teoricas', 'horas_practicas']);
        });
    }
};
