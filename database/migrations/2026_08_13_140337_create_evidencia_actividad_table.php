<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidenciaactividad', function (Blueprint $table) {
            $table->id();
            $table->string('urlDocumento', 255)->nullable();
            $table->string('orientador', 255)->nullable();
            $table->string('registroFotografico', 255)->nullable();
            $table->unsignedBigInteger('idActividad')->nullable();
            $table->timestamps();

            $table->foreign('idActividad')
                ->references('id')->on('actividad')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidenciaactividad');
    }
};