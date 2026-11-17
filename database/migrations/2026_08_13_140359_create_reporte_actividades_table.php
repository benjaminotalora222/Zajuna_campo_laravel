<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporteactividades', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255)->nullable();
            $table->integer('totalActividades')->nullable();
            $table->integer('totalParticipantes')->nullable();
            $table->decimal('promedioAsistencia', 5, 2)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->unsignedBigInteger('idProyecto')->nullable();
            $table->timestamps();

            $table->foreign('idProyecto')
                ->references('id')->on('proyectoinvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporteactividades');
    }
};