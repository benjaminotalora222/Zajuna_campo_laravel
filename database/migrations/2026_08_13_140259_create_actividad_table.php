<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividad', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255)->nullable();
            $table->string('tema', 255)->nullable();
            $table->dateTime('fechaHora')->nullable();
            $table->integer('duracion')->nullable();
            $table->string('espacioFisico', 255)->nullable();
            $table->string('responsable', 255)->nullable();
            $table->string('estado', 255)->nullable();
            $table->unsignedBigInteger('idProyecto')->nullable();
            $table->timestamps();

            $table->foreign('idProyecto')
                ->references('id')->on('proyectoinvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad');
    }
};