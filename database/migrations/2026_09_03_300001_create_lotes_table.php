<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad_inicial');          // cantidad cuando ingresó el lote
            $table->integer('cantidad_disponible');       // remanente (se descuenta con salidas FEFO)
            $table->date('fecha_entrada');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('notas')->nullable();
            $table->timestamps();

            $table->foreign('producto_id')
                ->references('id')->on('producto')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
