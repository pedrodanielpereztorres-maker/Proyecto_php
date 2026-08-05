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
        if (! Schema::hasTable('niveles_academicos')) {
            Schema::create('niveles_academicos', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre')->unique();
                $table->string('siglas')->unique();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('niveles_academicos', function (Blueprint $table): void {
                if (! Schema::hasColumn('niveles_academicos', 'siglas')) {
                    $table->string('siglas')->unique()->nullable()->after('nombre');
                }
            });

            if (Schema::hasColumn('niveles_academicos', 'abreviatura')) {
                DB::table('niveles_academicos')
                    ->whereNull('siglas')
                    ->update(['siglas' => DB::raw('abreviatura')]);

                Schema::table('niveles_academicos', function (Blueprint $table): void {
                    if (Schema::hasColumn('niveles_academicos', 'abreviatura')) {
                        $table->dropColumn('abreviatura');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('niveles_academicos');
    }
};
