<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarea', function (Blueprint $table) {
            if (!Schema::hasColumn('tarea', 'nombre'))         $table->string('nombre')->nullable()->after('id');
            if (!Schema::hasColumn('tarea', 'descripcion'))    $table->text('descripcion')->nullable()->after('nombre');
            // responsable como FK a users
            if (!Schema::hasColumn('tarea', 'responsable_id')) {
                $table->unsignedBigInteger('responsable_id')->nullable()->after('descripcion');
                $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tarea', 'fecha_entrega'))  $table->date('fecha_entrega')->nullable()->after('responsable_id');
            // estado: por_iniciar, en_progreso, completada
            if (!Schema::hasColumn('tarea', 'estado'))         $table->string('estado')->default('por_iniciar')->after('fecha_entrega');
            // prioridad: alta, media, baja
            if (!Schema::hasColumn('tarea', 'prioridad'))      $table->string('prioridad')->default('media')->after('estado');
            if (!Schema::hasColumn('tarea', 'proyecto_id')) {
                $table->unsignedBigInteger('proyecto_id')->nullable()->after('prioridad');
                $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->nullOnDelete();
            }
        });

        Schema::table('actividad', function (Blueprint $table) {
            if (!Schema::hasColumn('actividad', 'tipo'))           $table->string('tipo')->nullable()->after('id');
            if (!Schema::hasColumn('actividad', 'tema'))           $table->string('tema')->nullable()->after('tipo');
            if (!Schema::hasColumn('actividad', 'fecha_hora'))     $table->dateTime('fecha_hora')->nullable()->after('tema');
            if (!Schema::hasColumn('actividad', 'duracion'))       $table->integer('duracion')->nullable()->after('fecha_hora');
            if (!Schema::hasColumn('actividad', 'espacio_fisico')) $table->string('espacio_fisico')->nullable()->after('duracion');
            if (!Schema::hasColumn('actividad', 'responsable'))    $table->string('responsable')->nullable()->after('espacio_fisico');
            if (!Schema::hasColumn('actividad', 'estado'))         $table->string('estado')->default('pendiente')->after('responsable');
            if (!Schema::hasColumn('actividad', 'proyecto_id')) {
                $table->unsignedBigInteger('proyecto_id')->nullable()->after('estado');
                $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->nullOnDelete();
            }
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
