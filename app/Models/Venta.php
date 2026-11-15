<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table    = 'venta';
    protected $fillable = [
        'total', 'subtotal', 'iva', 'fechaVenta', 'idUsuario', 'cliente', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fechaVenta' => 'datetime',
            'subtotal'   => 'decimal:2',
            'iva'        => 'decimal:2',
            'total'      => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(VentaItem::class, 'venta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }
}
