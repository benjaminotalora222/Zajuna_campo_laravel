<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aprendices', function (Blueprint $table) {
            $table->id();
            // 1. Nombre completo
            $table->string('nombre_completo');
            // 2. Tipo de documento
            $table->string('tipo_documento', 50);
            $table->string('tipo_documento_otro', 100)->nullable();
            // 3. Número de documento
            $table->string('numero_documento', 50);
            // 4. Género
            $table->string('genero', 50);
            // 5. RH
            $table->string('rh', 10);
            // 6. EPS
            $table->string('eps', 150);
            // 7. Teléfono
            $table->string('telefono', 20);
            // 8. Correo electrónico
            $table->string('correo_electronico', 255);
            // 9. Tipo de programa
            $table->string('tipo_programa', 50);
            // 10. Programa de formación
            $table->string('programa_formacion', 255);
            $table->string('programa_formacion_otro', 255)->nullable();
            // 11. Ficha del programa
            $table->string('ficha_programa', 50);
            // 12. Auxilio o apoyo económico
            $table->string('apoyo_sena', 100)->nullable();
            $table->string('apoyo_sena_otro', 255)->nullable();
            // 13. Semillero de investigación
            $table->string('semillero', 100);
            // 14. Nombre del proyecto de investigación
            $table->text('nombre_proyecto');
            // 15. Instructor líder y asesores
            $table->text('instructor_lider_asesores');

            // Relaciones opcionales
            $table->unsignedBigInteger('proyecto_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->timestamps();

            $table->foreign('proyecto_id')->references('id')->on('proyectoinvestigacion')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aprendices');
    }
};
