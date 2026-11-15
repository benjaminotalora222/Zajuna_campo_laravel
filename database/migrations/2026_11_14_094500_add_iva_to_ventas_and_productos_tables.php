<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('venta', 'iva')) {
                $table->decimal('iva', 12, 2)->default(0)->after('subtotal');
            }
        });

        Schema::table('venta_items', function (Blueprint $table) {
            if (!Schema::hasColumn('venta_items', 'porcentaje_iva')) {
                $table->decimal('porcentaje_iva', 5, 2)->default(0)->after('precioUnitario');
            }
            if (!Schema::hasColumn('venta_items', 'valor_iva')) {
                $table->decimal('valor_iva', 12, 2)->default(0)->after('porcentaje_iva');
            }
        });

        Schema::table('producto', function (Blueprint $table) {
            if (!Schema::hasColumn('producto', 'iva')) {
                $table->decimal('iva', 5, 2)->default(0)->after('precio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'iva']);
        });

        Schema::table('venta_items', function (Blueprint $table) {
            $table->dropColumn(['porcentaje_iva', 'valor_iva']);
        });

        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('iva');
        });
    }
};

