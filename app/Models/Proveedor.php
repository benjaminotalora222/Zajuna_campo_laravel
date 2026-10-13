<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table      = 'proveedor';
    protected $fillable   = ['nombre', 'codigo', 'tipo', 'correo', 'telefono', 'estado'];

    protected function casts(): array
    {
        return ['estado' => 'boolean'];
    }

    public static function generarCodigo(): string
    {
        $maxId = static::max('id') ?? 0;
        $next = $maxId + 1;
        $codigo = 'PRV-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        while (static::where('codigo', $codigo)->exists()) {
            $next++;
            $codigo = 'PRV-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        }
        return $codigo;
    }
}
