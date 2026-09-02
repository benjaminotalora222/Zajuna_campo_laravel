<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Reporte', function (Blueprint $table) {
            $table->increments('idReporte');
            $table->string('tipo', 255)->nullable();
            $table->date('fechaInicio')->nullable();
            $table->date('fechaFin')->nullable();
            $table->string('formato', 255)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->unsignedInteger('idVenta')->nullable();

            $table->foreign('idVenta')
                ->references('idVenta')->on('Venta')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Reporte');
    }
};