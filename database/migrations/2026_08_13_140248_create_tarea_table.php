<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Tarea', function (Blueprint $table) {
            $table->increments('idTarea');
            $table->string('nombre', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('responsable', 255)->nullable();
            $table->date('fechaEntrega')->nullable();
            $table->string('estado', 255)->nullable();
            $table->decimal('porcentajeAvance', 5, 2)->nullable();
            $table->unsignedInteger('idProyecto')->nullable();

            $table->foreign('idProyecto')
                ->references('idProyecto')->on('ProyectoInvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Tarea');
    }
};