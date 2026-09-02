<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('NotificacionProveedor', function (Blueprint $table) {
            $table->increments('idNotificacion');
            $table->string('asunto', 255)->nullable();
            $table->dateTime('fechaEnvio')->nullable();
            $table->string('estadoEnvio', 255)->nullable();
            $table->string('tipoNotificacion', 255)->nullable();
            $table->unsignedInteger('idOrden')->nullable();

            $table->foreign('idOrden')
                ->references('idOrden')->on('OrdenPedido')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('NotificacionProveedor');
    }
};