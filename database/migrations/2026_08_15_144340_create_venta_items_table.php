<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->unsignedBigInteger('idProducto')->nullable();
            $table->integer('cantidad')->default(1);
            $table->decimal('precioUnitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('venta')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_items');
    }
};
