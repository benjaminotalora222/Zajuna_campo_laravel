<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Inventario', function (Blueprint $table) {
            $table->increments('idInventario');
            $table->unsignedBigInteger('idProducto');
            $table->integer('cantidadActual')->default(0);
            $table->string('ubicacion', 255)->nullable();
            $table->dateTime('fechaActualizacion')->nullable()->useCurrent();

            $table->foreign('idProducto')
                ->references('id')->on('producto')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Inventario');
    }
};