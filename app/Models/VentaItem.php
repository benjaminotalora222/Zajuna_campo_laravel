<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaItem extends Model
{
    protected $table    = 'venta_items';
    protected $fillable = [
        'venta_id', 'idProducto', 'cantidad', 'precioUnitario', 'porcentaje_iva', 'valor_iva', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'precioUnitario' => 'decimal:2',
            'porcentaje_iva' => 'decimal:2',
            'valor_iva'      => 'decimal:2',
            'subtotal'       => 'decimal:2',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
