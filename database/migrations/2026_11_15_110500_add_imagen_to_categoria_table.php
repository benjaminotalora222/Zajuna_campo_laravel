<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categoria', function (Blueprint $table) {
            if (!Schema::hasColumn('categoria', 'imagen')) {
                $table->string('imagen', 255)->nullable()->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categoria', function (Blueprint $table) {
            if (Schema::hasColumn('categoria', 'imagen')) {
                $table->dropColumn('imagen');
            }
        });
    }
};
