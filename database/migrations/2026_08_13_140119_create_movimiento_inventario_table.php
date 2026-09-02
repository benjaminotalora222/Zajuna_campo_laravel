<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('MovimientoInventario', function (Blueprint $table) {
            $table->increments('idMovimiento');
            $table->string('tipo', 255)->nullable();
            $table->integer('cantidad')->nullable();
            $table->dateTime('fechaMovimiento')->nullable();
            $table->date('fechaVencimiento')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->unsignedInteger('idProducto')->nullable();

            $table->foreign('idProducto')
                ->references('idProducto')->on('Producto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('MovimientoInventario');
    }
};