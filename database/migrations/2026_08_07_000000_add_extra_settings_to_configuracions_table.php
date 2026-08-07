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
        Schema::table('configuracions', function (Blueprint $table) {
            $table->string('color_secundario')->default('#ffffff')->after('color_principal');
            $table->string('email_contacto')->nullable()->after('color_secundario');
            $table->string('telefono_contacto')->nullable()->after('email_contacto');
            $table->string('direccion')->nullable()->after('telefono_contacto');
            $table->string('pie_pagina_pdf')->nullable()->after('direccion');
            $table->string('director_academico')->nullable()->after('pie_pagina_pdf');
            $table->string('coordinador_general')->nullable()->after('director_academico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracions', function (Blueprint $table) {
            $table->dropColumn([
                'color_secundario',
                'email_contacto',
                'telefono_contacto',
                'direccion',
                'pie_pagina_pdf',
                'director_academico',
                'coordinador_general'
            ]);
        });
    }
};
