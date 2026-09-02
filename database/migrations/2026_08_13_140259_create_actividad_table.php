<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Actividad', function (Blueprint $table) {
            $table->increments('idActividad');
            $table->string('tipo', 255)->nullable();
            $table->string('tema', 255)->nullable();
            $table->dateTime('fechaHora')->nullable();
            $table->integer('duracion')->nullable();
            $table->string('espacioFisico', 255)->nullable();
            $table->string('responsable', 255)->nullable();
            $table->string('estado', 255)->nullable();
            $table->unsignedInteger('idProyecto')->nullable();

            $table->foreign('idProyecto')
                ->references('idProyecto')->on('ProyectoInvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Actividad');
    }
};