<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('profesor_restriccions')) {
            Schema::create('profesor_restriccions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('profesor_id')->constrained('profesors')->cascadeOnDelete();
                $table->string('dia_semana');
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->text('motivo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profesor_restriccions');
    }
};
