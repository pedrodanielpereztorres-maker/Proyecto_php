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
        Schema::table('jornada_parametros', function (Blueprint $table) {
            $table->foreignId('tipo_jornada_id')->nullable()->constrained('tipo_jornadas')->cascadeOnDelete();
        });

        // Migrar datos existentes (si los hay)
        $parametros = \Illuminate\Support\Facades\DB::table('jornada_parametros')->get();
        foreach ($parametros as $param) {
            // Verificar si el tipo de jornada ya existe para no duplicar
            $tipo = \Illuminate\Support\Facades\DB::table('tipo_jornadas')->where('nombre', $param->tipo_jornada)->first();
            if (!$tipo) {
                $tipoId = \Illuminate\Support\Facades\DB::table('tipo_jornadas')->insertGetId([
                    'nombre' => $param->tipo_jornada,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $tipoId = $tipo->id;
            }
            \Illuminate\Support\Facades\DB::table('jornada_parametros')
                ->where('id', $param->id)
                ->update(['tipo_jornada_id' => $tipoId]);
        }

        Schema::table('jornada_parametros', function (Blueprint $table) {
            $table->dropColumn('tipo_jornada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jornada_parametros', function (Blueprint $table) {
            $table->string('tipo_jornada')->nullable();
        });

        $parametros = \Illuminate\Support\Facades\DB::table('jornada_parametros')->get();
        foreach ($parametros as $param) {
            if ($param->tipo_jornada_id) {
                $tipo = \Illuminate\Support\Facades\DB::table('tipo_jornadas')->where('id', $param->tipo_jornada_id)->first();
                if ($tipo) {
                    \Illuminate\Support\Facades\DB::table('jornada_parametros')
                        ->where('id', $param->id)
                        ->update(['tipo_jornada' => $tipo->nombre]);
                }
            }
        }

        Schema::table('jornada_parametros', function (Blueprint $table) {
            $table->dropForeign(['tipo_jornada_id']);
            $table->dropColumn('tipo_jornada_id');
        });
    }
};
