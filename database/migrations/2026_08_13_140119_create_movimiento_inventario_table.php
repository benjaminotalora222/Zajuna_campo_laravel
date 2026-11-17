<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientoinventario', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255)->nullable();
            $table->integer('cantidad')->nullable();
            $table->dateTime('fechaMovimiento')->nullable();
            $table->date('fechaVencimiento')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->unsignedBigInteger('idProducto')->nullable();
            $table->timestamps();

            $table->foreign('idProducto')
                ->references('id')->on('producto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientoinventario');
    }
};