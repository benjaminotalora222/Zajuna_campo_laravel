<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('EvidenciaActividad', function (Blueprint $table) {
            $table->increments('idEvidencia');
            $table->string('urlDocumento', 255)->nullable();
            $table->string('orientador', 255)->nullable();
            $table->string('registroFotografico', 255)->nullable();
            $table->unsignedInteger('idActividad')->nullable();

            $table->foreign('idActividad')
                ->references('idActividad')->on('Actividad')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('EvidenciaActividad');
    }
};