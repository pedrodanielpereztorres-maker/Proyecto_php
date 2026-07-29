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
        if (Schema::hasTable('semestres')) {
            Schema::rename('semestres', 'periodos_academicos');
        } else if (!Schema::hasTable('periodos_academicos')) {
            Schema::create('periodos_academicos', function (Blueprint $table) {
                $table->id();
                $table->string('codigo')->unique();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_fin')->nullable();
                $table->boolean('activo')->default(false);
                $table->timestamps();
            });
        }

        Schema::table('periodos_academicos', function (Blueprint $table) {
            if (Schema::hasColumn('periodos_academicos', 'nombre') && !Schema::hasColumn('periodos_academicos', 'codigo')) {
                $table->renameColumn('nombre', 'codigo');
            }
            if (!Schema::hasColumn('periodos_academicos', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('codigo');
            }
            if (!Schema::hasColumn('periodos_academicos', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            }
        });

        if (Schema::hasTable('horarios')) {
            Schema::table('horarios', function (Blueprint $table) {
                if (Schema::hasColumn('horarios', 'semestre_id') && !Schema::hasColumn('horarios', 'periodo_academico_id')) {
                    $table->renameColumn('semestre_id', 'periodo_academico_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('horarios')) {
            Schema::table('horarios', function (Blueprint $table) {
                if (Schema::hasColumn('horarios', 'periodo_academico_id')) {
                    $table->renameColumn('periodo_academico_id', 'semestre_id');
                }
            });
        }

        Schema::table('periodos_academicos', function (Blueprint $table) {
            if (Schema::hasColumn('periodos_academicos', 'fecha_fin')) {
                $table->dropColumn('fecha_fin');
            }
            if (Schema::hasColumn('periodos_academicos', 'fecha_inicio')) {
                $table->dropColumn('fecha_inicio');
            }
            if (Schema::hasColumn('periodos_academicos', 'codigo')) {
                $table->renameColumn('codigo', 'nombre');
            }
        });

        if (Schema::hasTable('periodos_academicos')) {
            Schema::rename('periodos_academicos', 'semestres');
        }
    }
};
