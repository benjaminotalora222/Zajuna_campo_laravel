<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenpedido', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fechaGeneracion')->nullable();
            $table->string('estadoEnvio', 255)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->unsignedBigInteger('idProveedor')->nullable();
            $table->timestamps();

            $table->foreign('idProveedor')
                ->references('id')->on('proveedor')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenpedido');
    }
};