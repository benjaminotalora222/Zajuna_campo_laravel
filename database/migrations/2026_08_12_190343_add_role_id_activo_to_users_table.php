<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role_id después de email, FK a roles
            $table->unsignedBigInteger('role_id')->nullable()->default(null)->after('email');
            $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();

            // activo después de role_id
            $table->boolean('activo')->default(true)->after('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'activo']);
        });
    }
};
