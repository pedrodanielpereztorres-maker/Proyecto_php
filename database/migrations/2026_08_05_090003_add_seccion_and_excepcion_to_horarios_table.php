<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table): void {
            if (! Schema::hasColumn('horarios', 'seccion_id')) {
                $table->foreignId('seccion_id')
                    ->nullable()
                    ->constrained('secciones')
                    ->nullOnDelete()
                    ->after('espacio_id');
            }

            if (! Schema::hasColumn('horarios', 'omitir_validacion_capacidad')) {
                $table->boolean('omitir_validacion_capacidad')
                    ->default(false)
                    ->after('seccion_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table): void {
            if (Schema::hasColumn('horarios', 'omitir_validacion_capacidad')) {
                $table->dropColumn('omitir_validacion_capacidad');
            }

            if (Schema::hasColumn('horarios', 'seccion_id')) {
                $table->dropConstrainedForeignId('seccion_id');
            }
        });
    }
};
