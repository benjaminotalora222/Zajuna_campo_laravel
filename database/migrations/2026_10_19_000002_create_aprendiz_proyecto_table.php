<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aprendiz_proyecto', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('aprendiz_id');
            $table->timestamps();

            $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->cascadeOnDelete();
            $table->foreign('aprendiz_id')->references('id')->on('aprendices')->cascadeOnDelete();

            $table->unique(['proyecto_id', 'aprendiz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aprendiz_proyecto');
    }
};
