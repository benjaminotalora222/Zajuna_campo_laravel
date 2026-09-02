<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarea', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('id');
            $table->text('descripcion')->nullable()->after('nombre');
            // responsable como FK a users
            $table->unsignedBigInteger('responsable_id')->nullable()->after('descripcion');
            $table->date('fecha_entrega')->nullable()->after('responsable_id');
            // estado: por_iniciar, en_progreso, completada
            $table->string('estado')->default('por_iniciar')->after('fecha_entrega');
            // prioridad: alta, media, baja
            $table->string('prioridad')->default('media')->after('estado');
            $table->unsignedBigInteger('proyecto_id')->nullable()->after('prioridad');

            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->nullOnDelete();
        });

        Schema::table('actividad', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('id');
            $table->string('tema')->nullable()->after('tipo');
            $table->dateTime('fecha_hora')->nullable()->after('tema');
            $table->integer('duracion')->nullable()->after('fecha_hora');
            $table->string('espacio_fisico')->nullable()->after('duracion');
            $table->string('responsable')->nullable()->after('espacio_fisico');
            $table->string('estado')->default('pendiente')->after('responsable');
            $table->unsignedBigInteger('proyecto_id')->nullable()->after('estado');

            $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tarea', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn(['nombre','descripcion','responsable_id','fecha_entrega','estado','prioridad','proyecto_id']);
        });

        Schema::table('actividad', function (Blueprint $table) {
            $table->dropForeign(['proyecto_id']);
            $table->dropColumn(['tipo','tema','fecha_hora','duracion','espacio_fisico','responsable','estado','proyecto_id']);
        });
    }
};
