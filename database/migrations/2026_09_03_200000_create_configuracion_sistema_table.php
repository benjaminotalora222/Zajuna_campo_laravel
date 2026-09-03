<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 100)->unique();
            $table->text('valor')->nullable();
            $table->timestamps();
        });

        // Valores por defecto
        $defaults = [
            ['clave' => 'nombre_plataforma',        'valor' => 'Zajuna Go'],
            ['clave' => 'zona_horaria',             'valor' => 'America/Bogota'],
            ['clave' => 'idioma',                   'valor' => 'es'],
            ['clave' => 'notif_correo',             'valor' => '1'],
            ['clave' => 'notif_recordatorios',      'valor' => '1'],
            ['clave' => 'notif_alertas_criticas',   'valor' => '1'],
            ['clave' => 'seg_dos_pasos',            'valor' => '0'],
            ['clave' => 'seg_sesion_automatica',    'valor' => '1'],
        ];

        foreach ($defaults as $item) {
            DB::table('configuracion_sistema')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};
