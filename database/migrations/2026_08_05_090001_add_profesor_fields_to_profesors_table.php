<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesors', function (Blueprint $table): void {
            if (! Schema::hasColumn('profesors', 'codigo_interno')) {
                $table->string('codigo_interno')->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('profesors', 'direccion')) {
                $table->text('direccion')->nullable()->after('codigo_interno');
            }
            if (! Schema::hasColumn('profesors', 'telefono')) {
                $table->string('telefono')->nullable()->after('direccion');
            }
            if (! Schema::hasColumn('profesors', 'nivel_academico_id')) {
                $table->foreignId('nivel_academico_id')->nullable()->constrained('niveles_academicos')->nullOnDelete()->after('telefono');
            }
            if (! Schema::hasColumn('profesors', 'especialidad_id')) {
                $table->foreignId('especialidad_id')->nullable()->constrained('especialidades')->nullOnDelete()->after('nivel_academico_id');
            }
            if (! Schema::hasColumn('profesors', 'avatar')) {
                $table->string('avatar')->nullable()->after('especialidad_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profesors', function (Blueprint $table): void {
            if (Schema::hasColumn('profesors', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('profesors', 'especialidad_id')) {
                $table->dropConstrainedForeignId('especialidad_id');
            }
            if (Schema::hasColumn('profesors', 'nivel_academico_id')) {
                $table->dropConstrainedForeignId('nivel_academico_id');
            }
            if (Schema::hasColumn('profesors', 'telefono')) {
                $table->dropColumn('telefono');
            }
            if (Schema::hasColumn('profesors', 'direccion')) {
                $table->dropColumn('direccion');
            }
            if (Schema::hasColumn('profesors', 'codigo_interno')) {
                $table->dropUnique(['codigo_interno']);
                $table->dropColumn('codigo_interno');
            }
        });
    }
};
