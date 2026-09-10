<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientoinventario', function (Blueprint $table) {
            // producto_id normalizado (bigint, not nullable)
            $table->unsignedBigInteger('producto_id')->nullable()->after('id');
            // lote afectado (solo en salidas FEFO)
            $table->unsignedBigInteger('lote_id')->nullable()->after('producto_id');
            // usuario que registró el movimiento
            $table->unsignedBigInteger('user_id')->nullable()->after('lote_id');

            $table->foreign('producto_id')->references('id')->on('producto')->nullOnDelete();
            $table->foreign('lote_id')->references('id')->on('lotes')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimientoinventario', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->dropForeign(['lote_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['producto_id', 'lote_id', 'user_id']);
        });
    }
};
