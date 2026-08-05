<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carreras', function (Blueprint $table): void {
            if (! Schema::hasColumn('carreras', 'codigo')) {
                $table->string('codigo')->nullable();
            }
            if (! Schema::hasColumn('carreras', 'nivel_academico_id')) {
                $table->foreignId('nivel_academico_id')->nullable()->constrained('niveles_academicos')->nullOnDelete();
            }
            if (! Schema::hasColumn('carreras', 'departamento_id')) {
                $table->foreignId('departamento_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        if (Schema::hasColumn('carreras', 'codigo')) {
            $rows = DB::table('carreras')->whereNull('codigo')->get();
            foreach ($rows as $row) {
                DB::table('carreras')->where('id', $row->id)->update([
                    'codigo' => 'CAR-' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                ]);
            }

            if (Schema::hasColumn('carreras', 'codigo')) {
                // Leave the column nullable if DBAL is not installed.
            }
        }
    }

    public function down(): void
    {
        Schema::table('carreras', function (Blueprint $table): void {
            if (Schema::hasColumn('carreras', 'departamento_id')) {
                $table->dropConstrainedForeignId('departamento_id');
            }
            if (Schema::hasColumn('carreras', 'nivel_academico_id')) {
                $table->dropConstrainedForeignId('nivel_academico_id');
            }
            if (Schema::hasColumn('carreras', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });
    }
};
