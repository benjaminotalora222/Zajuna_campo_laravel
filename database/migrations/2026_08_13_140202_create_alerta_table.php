<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerta', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 255)->nullable();
            $table->string('mensaje', 255)->nullable();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->boolean('leida')->nullable()->default(false);
            $table->unsignedBigInteger('idProducto')->nullable();
            $table->timestamps();

            $table->foreign('idProducto')
                ->references('id')->on('producto')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerta');
    }
};