<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'idProducto'))
                $table->unsignedBigInteger('idProducto')->nullable()->after('idUsuario');
            if (!Schema::hasColumn('venta', 'cliente'))
                $table->string('cliente', 255)->nullable()->after('idProducto');
            if (!Schema::hasColumn('venta', 'observaciones'))
                $table->text('observaciones')->nullable()->after('cliente');
        });
    }

    public function down(): void {}
};
