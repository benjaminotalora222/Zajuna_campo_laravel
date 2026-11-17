<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacionproveedor', function (Blueprint $table) {
            $table->id();
            $table->string('asunto', 255)->nullable();
            $table->dateTime('fechaEnvio')->nullable();
            $table->string('estadoEnvio', 255)->nullable();
            $table->string('tipoNotificacion', 255)->nullable();
            $table->unsignedBigInteger('idOrden')->nullable();
            $table->timestamps();

            $table->foreign('idOrden')
                ->references('id')->on('ordenpedido')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacionproveedor');
    }
};