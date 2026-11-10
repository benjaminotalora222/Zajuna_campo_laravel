<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPedido extends Model
{
    protected $table = 'ordenpedido';

    protected $fillable = [
        'numero_orden',
        'proveedor_id',
        'producto_id',
        'stock_actual',
        'stock_minimo',
        'cantidad_solicitada',
        'estado',
        'fecha_generacion',
        'fecha_envio',
        'correo_destino',
        'observaciones',
        'detalles',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'datetime',
            'fecha_envio'      => 'datetime',
            'detalles'         => 'array',
        ];
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(NotificacionProveedor::class, 'orden_pedido_id');
    }

    public static function generarNumeroOrden(): string
    {
        $prefijo = 'ORD-' . date('Ym') . '-';
        $conteoMes = static::where('numero_orden', 'like', $prefijo . '%')->count();
        $numero = str_pad((string)($conteoMes + 1), 4, '0', STR_PAD_LEFT);
        $codigo = $prefijo . $numero;

        while (static::where('numero_orden', $codigo)->exists()) {
            $conteoMes++;
            $numero = str_pad((string)($conteoMes + 1), 4, '0', STR_PAD_LEFT);
            $codigo = $prefijo . $numero;
        }

        return $codigo;
    }
}

