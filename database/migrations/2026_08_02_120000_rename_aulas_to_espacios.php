<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('espacios')) {
            Schema::create('espacios', function (Blueprint $table) {
                $table->id();
                $table->string('codigo')->unique();
                $table->string('nombre')->unique();
                $table->integer('capacidad_maxima');
                $table->foreignId('tipo_espacio_id')
                    ->constrained('tipo_espacios')
                    ->onDelete('restrict');
                $table->enum('estatus_operativo', ['activo', 'inactivo', 'mantenimiento'])
                    ->default('activo');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('aulas') && ! Schema::hasTable('espacios')) {
            $aulas = DB::table('aulas')->get();
            foreach ($aulas as $aula) {
                DB::table('espacios')->insert([
                    'id' => $aula->id,
                    'codigo' => $aula->codigo,
                    'nombre' => 'Espacio_' . $aula->codigo,
                    'capacidad_maxima' => $aula->capacidad,
                    'tipo_espacio_id' => $aula->tipo_espacio_id,
                    'estatus_operativo' => 'activo',
                    'created_at' => $aula->created_at,
                    'updated_at' => $aula->updated_at,
                ]);
            }
        }

        if (Schema::hasTable('horarios') && Schema::hasColumn('horarios', 'aula_id')) {
            Schema::table('horarios', function (Blueprint $table) {
                $table->dropForeign(['aula_id']);
                $table->renameColumn('aula_id', 'espacio_id');
                $table->foreign('espacio_id')
                    ->references('id')
                    ->on('espacios')
                    ->onDelete('cascade');
            });
        }

        if (Schema::hasTable('aulas')) {
            Schema::dropIfExists('aulas');
        }
    }

    public function down(): void
    {
        // Recrear tabla aulas
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->integer('capacidad');
            $table->foreignId('tipo_espacio_id')
                ->constrained('tipo_espacios');
            $table->timestamps();
        });

        // Copiar datos de espacios a aulas
        $espacios = DB::table('espacios')->get();
        foreach ($espacios as $espacio) {
            DB::table('aulas')->insert([
                'id' => $espacio->id,
                'codigo' => $espacio->codigo,
                'capacidad' => $espacio->capacidad_maxima,
                'tipo_espacio_id' => $espacio->tipo_espacio_id,
                'created_at' => $espacio->created_at,
                'updated_at' => $espacio->updated_at,
            ]);
        }

        // Revertir FK en horarios
        Schema::table('horarios', function (Blueprint $table) {
            $table->dropForeign('horarios_espacio_id_foreign');
            $table->renameColumn('espacio_id', 'aula_id');
            $table->foreign('aula_id')->references('id')->on('aulas')->onDelete('cascade');
        });

        // Eliminar tabla espacios
        Schema::dropIfExists('espacios');
    }
};
