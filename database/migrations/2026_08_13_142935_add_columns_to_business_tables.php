<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Producto
        Schema::table('producto', function (Blueprint $table) {
            if (!Schema::hasColumn('producto', 'nombre'))         $table->string('nombre', 255)->nullable();
            if (!Schema::hasColumn('producto', 'precio'))         $table->decimal('precio', 10, 2)->nullable();
            if (!Schema::hasColumn('producto', 'stockActual'))    $table->integer('stockActual')->nullable();
            if (!Schema::hasColumn('producto', 'stockMinimo'))    $table->integer('stockMinimo')->nullable();
            if (!Schema::hasColumn('producto', 'codigoBarras'))   $table->string('codigoBarras', 255)->nullable();
            if (!Schema::hasColumn('producto', 'diasPerecederoMax')) $table->integer('diasPerecederoMax')->nullable();
        });

        // Proveedor
        Schema::table('proveedor', function (Blueprint $table) {
            if (!Schema::hasColumn('proveedor', 'nombre'))    $table->string('nombre', 255)->nullable();
            if (!Schema::hasColumn('proveedor', 'tipo'))      $table->string('tipo', 255)->nullable();
            if (!Schema::hasColumn('proveedor', 'correo'))    $table->string('correo', 255)->nullable();
            if (!Schema::hasColumn('proveedor', 'telefono'))  $table->string('telefono', 255)->nullable();
            if (!Schema::hasColumn('proveedor', 'estado'))    $table->boolean('estado')->nullable();
        });

        // Venta
        Schema::table('venta', function (Blueprint $table) {
            if (!Schema::hasColumn('venta', 'cantidad'))       $table->integer('cantidad')->nullable();
            if (!Schema::hasColumn('venta', 'precioUnitario')) $table->decimal('precioUnitario', 10, 2)->nullable();
            if (!Schema::hasColumn('venta', 'total'))          $table->double('total')->nullable();
            if (!Schema::hasColumn('venta', 'fechaVenta'))     $table->dateTime('fechaVenta')->nullable();
            if (!Schema::hasColumn('venta', 'idUsuario'))      $table->unsignedBigInteger('idUsuario')->nullable();
        });

        // Alerta
        Schema::table('alerta', function (Blueprint $table) {
            if (!Schema::hasColumn('alerta', 'tipo'))             $table->string('tipo', 255)->nullable();
            if (!Schema::hasColumn('alerta', 'mensaje'))          $table->string('mensaje', 255)->nullable();
            if (!Schema::hasColumn('alerta', 'fechaGeneracion'))  $table->dateTime('fechaGeneracion')->nullable();
            if (!Schema::hasColumn('alerta', 'leida'))            $table->boolean('leida')->nullable()->default(false);
            if (!Schema::hasColumn('alerta', 'idProducto'))       $table->unsignedBigInteger('idProducto')->nullable();
        });

        // LogAuditoria
        Schema::table('logauditoria', function (Blueprint $table) {
            if (!Schema::hasColumn('logauditoria', 'idUsuario'))             $table->unsignedBigInteger('idUsuario')->nullable();
            if (!Schema::hasColumn('logauditoria', 'modulo'))                $table->string('modulo', 255)->nullable();
            if (!Schema::hasColumn('logauditoria', 'descripcionOperacion'))  $table->string('descripcionOperacion', 255)->nullable();
            if (!Schema::hasColumn('logauditoria', 'fechaHora'))             $table->dateTime('fechaHora')->nullable();
        });

        // ProyectoInvestigacion
        Schema::table('proyectoinvestigacion', function (Blueprint $table) {
            if (!Schema::hasColumn('proyectoinvestigacion', 'nombre'))           $table->string('nombre', 255)->nullable();
            if (!Schema::hasColumn('proyectoinvestigacion', 'objetivos'))        $table->text('objetivos')->nullable();
            if (!Schema::hasColumn('proyectoinvestigacion', 'justificacion'))    $table->text('justificacion')->nullable();
            if (!Schema::hasColumn('proyectoinvestigacion', 'marcoTeorico'))     $table->text('marcoTeorico')->nullable();
            if (!Schema::hasColumn('proyectoinvestigacion', 'instructores'))     $table->text('instructores')->nullable();
            if (!Schema::hasColumn('proyectoinvestigacion', 'porcentajeAvance')) $table->decimal('porcentajeAvance', 5, 2)->nullable()->default(0);
        });

        // MovimientoInventario
        Schema::table('movimientoinventario', function (Blueprint $table) {
            if (!Schema::hasColumn('movimientoinventario', 'tipo'))            $table->string('tipo', 255)->nullable();
            if (!Schema::hasColumn('movimientoinventario', 'cantidad'))        $table->integer('cantidad')->nullable();
            if (!Schema::hasColumn('movimientoinventario', 'fechaMovimiento')) $table->dateTime('fechaMovimiento')->nullable();
            if (!Schema::hasColumn('movimientoinventario', 'fechaVencimiento'))$table->date('fechaVencimiento')->nullable();
            if (!Schema::hasColumn('movimientoinventario', 'motivo'))          $table->string('motivo', 255)->nullable();
            if (!Schema::hasColumn('movimientoinventario', 'idProducto'))      $table->unsignedBigInteger('idProducto')->nullable();
        });
    }

    public function down(): void
    {
        // No se revierten para no perder datos
    }
};
