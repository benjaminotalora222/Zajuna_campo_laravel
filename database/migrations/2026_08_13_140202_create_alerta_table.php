<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Alerta', function (Blueprint $table) {
            $table->increments('idAlerta');
            $table->string('tipo', 255)->nullable();
            $table->string('mensaje', 255)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->boolean('leida')->nullable();
            $table->unsignedInteger('idProducto')->nullable();

            $table->foreign('idProducto')
                ->references('idProducto')->on('Producto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Alerta');
    }
};