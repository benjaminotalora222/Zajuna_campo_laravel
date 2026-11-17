<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255)->nullable();
            $table->date('fechaInicio')->nullable();
            $table->date('fechaFin')->nullable();
            $table->string('formato', 255)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->unsignedBigInteger('idVenta')->nullable();
            $table->timestamps();

            $table->foreign('idVenta')
                ->references('id')->on('venta')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte');
    }
};