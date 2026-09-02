<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyectoinvestigacion', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('nombre');
            // estado: en_progreso, planificado, completado, pausado
            $table->string('estado')->default('planificado')->after('descripcion');
            $table->date('fecha_inicio')->nullable()->after('estado');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->unsignedBigInteger('responsable_id')->nullable()->after('fecha_fin');
            $table->string('imagen')->nullable()->after('responsable_id');

            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proyectoinvestigacion', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn(['descripcion','estado','fecha_inicio','fecha_fin','responsable_id','imagen']);
        });
    }
};
