<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cronograma', function (Blueprint $table) {
            $table->id();
            $table->string('espacioFisico', 255)->nullable();
            $table->date('fechaVisita')->nullable();
            $table->unsignedBigInteger('idActividad')->nullable();
            $table->timestamps();

            $table->foreign('idActividad')
                ->references('id')->on('actividad')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma');
    }
};