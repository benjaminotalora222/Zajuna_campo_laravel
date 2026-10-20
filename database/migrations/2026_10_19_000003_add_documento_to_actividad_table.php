<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->string('documento', 255)->nullable()->after('espacio_fisico');
            $table->string('documento_nombre', 255)->nullable()->after('documento');
            $table->json('documentos')->nullable()->after('documento_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->dropColumn(['documento', 'documento_nombre', 'documentos']);
        });
    }
};
