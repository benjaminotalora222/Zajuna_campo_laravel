<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            [
                'id'          => 1,
                'nombre'      => 'superadmin',
                'descripcion' => 'Acceso total: gestiona todos los módulos, genera reportes y configura usuarios y parámetros del sistema.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 2,
                'nombre'      => 'operativo',
                'descripcion' => 'Registra ventas, inventarios, transferencias, shows y avances de investigación.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'id'          => 3,
                'nombre'      => 'proveedor',
                'descripcion' => 'Acceso de solo lectura para consultar sus propias ventas e inventario en el Minimarket.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}