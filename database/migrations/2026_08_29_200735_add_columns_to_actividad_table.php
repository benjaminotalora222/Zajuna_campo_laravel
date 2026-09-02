<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('tema');
            $table->integer('progreso')->default(0)->after('descripcion'); // 0-100
            $table->date('fecha_limite')->nullable()->after('progreso');
            $table->unsignedBigInteger('responsable_id')->nullable()->after('fecha_limite');

            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn(['descripcion', 'progreso', 'fecha_limite', 'responsable_id']);
        });
    }
};
