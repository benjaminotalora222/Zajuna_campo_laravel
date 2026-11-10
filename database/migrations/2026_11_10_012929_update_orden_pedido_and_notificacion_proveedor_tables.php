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
        // Actualizar tabla ordenpedido
        if (Schema::hasTable('ordenpedido')) {
            Schema::table('ordenpedido', function (Blueprint $table) {
                if (!Schema::hasColumn('ordenpedido', 'numero_orden')) {
                    $table->string('numero_orden', 50)->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('ordenpedido', 'proveedor_id')) {
                    $table->unsignedBigInteger('proveedor_id')->nullable()->after('numero_orden');
                }
                if (!Schema::hasColumn('ordenpedido', 'producto_id')) {
                    $table->unsignedBigInteger('producto_id')->nullable()->after('proveedor_id');
                }
                if (!Schema::hasColumn('ordenpedido', 'stock_actual')) {
                    $table->integer('stock_actual')->default(0)->after('producto_id');
                }
                if (!Schema::hasColumn('ordenpedido', 'stock_minimo')) {
                    $table->integer('stock_minimo')->default(0)->after('stock_actual');
                }
                if (!Schema::hasColumn('ordenpedido', 'cantidad_solicitada')) {
                    $table->integer('cantidad_solicitada')->default(0)->after('stock_minimo');
                }
                if (!Schema::hasColumn('ordenpedido', 'estado')) {
                    $table->string('estado', 50)->default('enviada')->after('cantidad_solicitada');
                }
                if (!Schema::hasColumn('ordenpedido', 'fecha_generacion')) {
                    $table->dateTime('fecha_generacion')->nullable()->after('estado');
                }
                if (!Schema::hasColumn('ordenpedido', 'fecha_envio')) {
                    $table->dateTime('fecha_envio')->nullable()->after('fecha_generacion');
                }
                if (!Schema::hasColumn('ordenpedido', 'correo_destino')) {
                    $table->string('correo_destino', 255)->nullable()->after('fecha_envio');
                }
                if (!Schema::hasColumn('ordenpedido', 'observaciones')) {
                    $table->text('observaciones')->nullable()->after('correo_destino');
                }
                if (!Schema::hasColumn('ordenpedido', 'detalles')) {
                    $table->json('detalles')->nullable()->after('observaciones');
                }
                if (!Schema::hasColumn('ordenpedido', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('detalles');
                }
            });
        }

        // Actualizar tabla notificacionproveedor
        if (Schema::hasTable('notificacionproveedor')) {
            Schema::table('notificacionproveedor', function (Blueprint $table) {
                if (!Schema::hasColumn('notificacionproveedor', 'orden_pedido_id')) {
                    $table->unsignedBigInteger('orden_pedido_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'proveedor_id')) {
                    $table->unsignedBigInteger('proveedor_id')->nullable()->after('orden_pedido_id');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'asunto')) {
                    $table->string('asunto', 255)->nullable()->after('proveedor_id');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'correo_destino')) {
                    $table->string('correo_destino', 255)->nullable()->after('asunto');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'mensaje')) {
                    $table->text('mensaje')->nullable()->after('correo_destino');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'estado_envio')) {
                    $table->string('estado_envio', 50)->default('enviado')->after('mensaje');
                }
                if (!Schema::hasColumn('notificacionproveedor', 'fecha_envio')) {
                    $table->dateTime('fecha_envio')->nullable()->after('estado_envio');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se eliminan columnas para preservar datos en reversión
    }
};
