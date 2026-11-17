<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarea', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('responsable', 255)->nullable();
            $table->date('fechaEntrega')->nullable();
            $table->string('estado', 255)->nullable();
            $table->decimal('porcentajeAvance', 5, 2)->nullable()->default(0);
            $table->unsignedBigInteger('idProyecto')->nullable();
            $table->timestamps();

            $table->foreign('idProyecto')
                ->references('id')->on('proyectoinvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarea');
    }
};