<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Venta', function (Blueprint $table) {
            $table->increments('idVenta');
            $table->integer('cantidad')->nullable();
            $table->decimal('precioUnitario', 10, 2)->nullable();
            $table->double('total')->nullable();
            $table->dateTime('fechaVenta')->nullable();
            $table->unsignedInteger('idUsuario')->nullable();

            $table->foreign('idUsuario')
                ->references('idUsuario')->on('Usuario')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Venta');
    }
};