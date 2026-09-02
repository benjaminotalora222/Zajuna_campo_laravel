<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Proveedor', function (Blueprint $table) {
            $table->increments('idProveedor');
            $table->string('nombre', 255)->nullable();
            $table->string('tipo', 255)->nullable();
            $table->string('correo', 255)->nullable();
            $table->string('telefono', 255)->nullable();
            $table->boolean('estado')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Proveedor');
    }
};