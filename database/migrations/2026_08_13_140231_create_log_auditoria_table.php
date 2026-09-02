<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('LogAuditoria', function (Blueprint $table) {
            $table->increments('idLog');
            $table->unsignedInteger('idUsuario')->nullable();
            $table->string('modulo', 255)->nullable();
            $table->string('descripcionOperacion', 255)->nullable();
            $table->dateTime('fechaHora')->nullable();

            $table->foreign('idUsuario')
                ->references('idUsuario')->on('Usuario')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('LogAuditoria');
    }
};