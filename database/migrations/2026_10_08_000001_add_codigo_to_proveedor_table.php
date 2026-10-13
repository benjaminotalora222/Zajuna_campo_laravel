<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedor', function (Blueprint $table) {
            if (!Schema::hasColumn('proveedor', 'codigo')) {
                $table->string('codigo', 50)->nullable()->unique()->after('id');
            }
        });

        // Asignar códigos únicos iniciales a los proveedores existentes que no tengan código
        $proveedores = DB::table('proveedor')->whereNull('codigo')->orWhere('codigo', '')->orderBy('id')->get();
        foreach ($proveedores as $prov) {
            $codigo = 'PRV-' . str_pad($prov->id, 3, '0', STR_PAD_LEFT);
            DB::table('proveedor')->where('id', $prov->id)->update(['codigo' => $codigo]);
        }
    }

    public function down(): void
    {
        Schema::table('proveedor', function (Blueprint $table) {
            if (Schema::hasColumn('proveedor', 'codigo')) {
                $table->dropColumn('codigo');
            }
        });
    }
};
