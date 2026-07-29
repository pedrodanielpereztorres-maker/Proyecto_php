<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('periodos_academicos')) {
            return;
        }

        if (Schema::hasColumn('semestres', 'nombre') && ! Schema::hasColumn('periodos_academicos', 'codigo')) {
            DB::table('periodos_academicos')->insertUsing(
                ['codigo', 'fecha_inicio', 'fecha_fin', 'activo', 'created_at', 'updated_at'],
                DB::table('semestres')->select(
                    'nombre',
                    DB::raw('NULL as fecha_inicio'),
                    DB::raw('NULL as fecha_fin'),
                    'activo',
                    'created_at',
                    'updated_at'
                )
            );
        }

        if (Schema::hasColumn('horarios', 'semestre_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->foreignId('periodo_academico_id')->nullable()->constrained('periodos_academicos')->nullOnDelete();
            });

            $periodos = DB::table('periodos_academicos')->pluck('id', 'codigo');
            foreach ($periodos as $codigo => $id) {
                DB::table('horarios')
                    ->where('semestre_id', function ($query) use ($codigo) {
                        $query->select('id')->from('semestres')->where('nombre', $codigo);
                    })
                    ->update(['periodo_academico_id' => $id]);
            }

            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['semestre_id']);
                $table->dropColumn('semestre_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('horarios', 'periodo_academico_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->foreignId('semestre_id')->nullable()->constrained()->nullOnDelete();
            });

            $semestres = DB::table('semestres')->pluck('id', 'nombre');
            foreach ($semestres as $nombre => $id) {
                DB::table('horarios')
                    ->where('periodo_academico_id', function ($query) use ($nombre) {
                        $query->select('id')->from('periodos_academicos')->where('codigo', $nombre);
                    })
                    ->update(['semestre_id' => $id]);
            }

            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['periodo_academico_id']);
                $table->dropColumn('periodo_academico_id');
            });
        }
    }
};
