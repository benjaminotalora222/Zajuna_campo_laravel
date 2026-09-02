<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ProyectoInvestigacion', function (Blueprint $table) {
            $table->increments('idProyecto');
            $table->string('nombre', 255)->nullable();
            $table->text('objetivos')->nullable();
            $table->text('justificacion')->nullable();
            $table->text('marcoTeorico')->nullable();
            $table->text('instructores')->nullable();
            $table->decimal('porcentajeAvance', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ProyectoInvestigacion');
    }
};