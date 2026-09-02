<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Cronograma', function (Blueprint $table) {
            $table->increments('idCronograma');
            $table->string('espacioFisico', 255)->nullable();
            $table->date('fechaVisita')->nullable();
            $table->unsignedInteger('idActividad')->nullable();

            $table->foreign('idActividad')
                ->references('idActividad')->on('Actividad')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Cronograma');
    }
};