<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('LogAuditoria', function (Blueprint $table) {
            $table->string('accion', 100)->nullable()->after('modulo');
            $table->string('estado', 20)->default('exitoso')->after('descripcionOperacion'); // exitoso | fallido
        });
    }

    public function down(): void
    {
        Schema::table('LogAuditoria', function (Blueprint $table) {
            $table->dropColumn(['accion', 'estado']);
        });
    }
};
