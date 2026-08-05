<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('especialidades')) {
            Schema::create('especialidades', function (Blueprint $table): void {
                $table->id();
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->boolean('activo')->default(true);
                $table->foreignId('carrera_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        } else {
            Schema::table('especialidades', function (Blueprint $table): void {
                if (! Schema::hasColumn('especialidades', 'carrera_id')) {
                    $table->foreignId('carrera_id')->nullable()->constrained()->nullOnDelete();
                }
                if (! Schema::hasColumn('especialidades', 'activo')) {
                    $table->boolean('activo')->default(true);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};
