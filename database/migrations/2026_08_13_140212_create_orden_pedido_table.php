<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('OrdenPedido', function (Blueprint $table) {
            $table->increments('idOrden');
            $table->dateTime('fechaGeneracion')->nullable();
            $table->string('estadoEnvio', 255)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->unsignedInteger('idProveedor')->nullable();

            $table->foreign('idProveedor')
                ->references('idProveedor')->on('Proveedor')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('OrdenPedido');
    }
};