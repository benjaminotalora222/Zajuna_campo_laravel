<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSistema extends Model
{
    protected $table    = 'configuracion_sistema';
    protected $fillable = ['clave', 'valor'];

    /**
     * Obtiene el valor de una clave. Retorna $default si no existe.
     */
    public static function get(string $clave, mixed $default = null): mixed
    {
        $row = static::where('clave', $clave)->first();
        return $row ? $row->valor : $default;
    }

    /**
     * Guarda o actualiza el valor de una clave.
     */
    public static function set(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
    }
}
