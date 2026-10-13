<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            if (!Schema::hasColumn('actividad', 'evento_id')) {
                $table->unsignedBigInteger('evento_id')->nullable()->after('proyecto_id');
                $table->foreign('evento_id')->references('id')->on('evento')->cascadeOnDelete();
            }
        });

        // Sincronizar eventos existentes hacia la tabla actividad si no existen
        $eventos = DB::table('evento')->get();
        foreach ($eventos as $ev) {
            $existe = DB::table('actividad')->where('evento_id', $ev->id)->exists();
            if (!$existe) {
                $fechaHora = null;
                if (!empty($ev->fecha)) {
                    $fechaStr = substr($ev->fecha, 0, 10);
                    $horaStr = !empty($ev->hora_inicio) ? $ev->hora_inicio : '00:00:00';
                    $fechaHora = "{$fechaStr} {$horaStr}";
                }

                DB::table('actividad')->insert([
                    'tema'           => $ev->titulo,
                    'descripcion'    => $ev->descripcion,
                    'tipo'           => $ev->tipo,
                    'espacio_fisico' => $ev->espacio,
                    'fecha_hora'     => $fechaHora,
                    'fecha_limite'   => !empty($ev->fecha) ? substr($ev->fecha, 0, 10) : null,
                    'responsable_id' => $ev->creado_por,
                    'estado'         => 'pendiente',
                    'progreso'       => 0,
                    'evento_id'      => $ev->id,
                    'created_at'     => $ev->created_at ?? now(),
                    'updated_at'     => $ev->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('actividad', function (Blueprint $table) {
            if (Schema::hasColumn('actividad', 'evento_id')) {
                $table->dropForeign(['evento_id']);
                $table->dropColumn('evento_id');
            }
        });
    }
};
