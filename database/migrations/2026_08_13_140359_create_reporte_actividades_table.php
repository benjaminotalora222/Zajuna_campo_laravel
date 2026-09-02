<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ReporteActividades', function (Blueprint $table) {
            $table->increments('idReporte');
            $table->string('tipo', 255)->nullable();
            $table->integer('totalActividades')->nullable();
            $table->integer('totalParticipantes')->nullable();
            $table->decimal('promedioAsistencia', 5, 2)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->unsignedInteger('idProyecto')->nullable();

            $table->foreign('idProyecto')
                ->references('idProyecto')->on('ProyectoInvestigacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ReporteActividades');
    }
};