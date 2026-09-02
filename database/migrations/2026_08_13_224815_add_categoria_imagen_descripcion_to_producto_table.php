<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            if (!Schema::hasColumn('producto', 'descripcion'))  $table->text('descripcion')->nullable();
            if (!Schema::hasColumn('producto', 'categoria'))    $table->string('categoria', 100)->nullable();
            if (!Schema::hasColumn('producto', 'unidad'))       $table->string('unidad', 50)->nullable()->default('kg');
            if (!Schema::hasColumn('producto', 'imagen'))       $table->string('imagen', 500)->nullable();
            if (!Schema::hasColumn('producto', 'activo'))       $table->boolean('activo')->nullable()->default(true);
        });
    }

    public function down(): void {}
};
