<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionProveedor extends Model
{
    protected $table = 'notificacionproveedor';

    protected $fillable = [
        'orden_pedido_id',
        'proveedor_id',
        'asunto',
        'correo_destino',
        'mensaje',
        'estado_envio',
        'fecha_envio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_envio' => 'datetime',
        ];
    }

    public function ordenPedido()
    {
        return $this->belongsTo(OrdenPedido::class, 'orden_pedido_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}

