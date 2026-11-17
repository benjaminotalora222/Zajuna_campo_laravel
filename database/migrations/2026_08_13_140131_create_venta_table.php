<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->nullable();
            $table->decimal('precioUnitario', 10, 2)->nullable();
            $table->double('total')->nullable();
            $table->dateTime('fechaVenta')->nullable();
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->timestamps();

            $table->foreign('idUsuario')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta');
    }
};