<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consignacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proveedor_id');
            $table->string('nombre_producto');
            $table->string('sku')->nullable();
            $table->string('unidad')->nullable();
            $table->integer('stock_disponible')->default(0);
            $table->date('fecha_vencimiento')->nullable();
            // estado: disponible, proximo_vencer, agotado
            $table->string('estado')->default('disponible');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('proveedor_id')->references('id')->on('proveedor')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consignacion');
    }
};
