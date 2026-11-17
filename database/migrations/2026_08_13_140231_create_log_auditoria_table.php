<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('LogAuditoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->string('modulo', 255)->nullable();
            $table->string('descripcionOperacion', 255)->nullable();
            $table->dateTime('fechaHora')->nullable();
            $table->timestamps();

            $table->foreign('idUsuario')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('LogAuditoria');
    }
};