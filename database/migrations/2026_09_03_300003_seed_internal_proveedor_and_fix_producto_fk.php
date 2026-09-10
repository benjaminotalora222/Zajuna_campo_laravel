<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Insertar proveedor interno si no existe
        $exists = DB::table('proveedor')->where('tipo', 'interno')->exists();
        if (! $exists) {
            DB::table('proveedor')->insert([
                'nombre'     => 'Producción de Centros',
                'tipo'       => 'interno',
                'correo'     => null,
                'telefono'   => null,
                'estado'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Asignar el proveedor interno a productos que aún no tienen proveedor_id
        $interno = DB::table('proveedor')->where('tipo', 'interno')->value('id');
        DB::table('producto')->whereNull('proveedor_id')->update(['proveedor_id' => $interno]);
    }

    public function down(): void
    {
        // No revertimos el proveedor interno para no romper FKs existentes
    }
};
