<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->foreignId('tipo_espacio_id')
                ->nullable()
                ->after('tipo')
                ->constrained('tipo_espacios')
                ->nullOnDelete();
        });

        $valores = DB::table('aulas')->whereNotNull('tipo')->distinct()->pluck('tipo');

        foreach ($valores as $nombre) {
            $id = DB::table('tipo_espacios')->where('nombre', $nombre)->value('id');

            if (! $id) {
                $id = DB::table('tipo_espacios')->insertGetId([
                    'nombre' => $nombre,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('aulas')->where('tipo', $nombre)->update(['tipo_espacio_id' => $id]);
        }

        Schema::table('aulas', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            $table->string('tipo')->default('Teoría')->after('capacidad');
        });

        Schema::table('aulas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_espacio_id');
        });
    }
};