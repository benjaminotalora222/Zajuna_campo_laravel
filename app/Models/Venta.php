<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table    = 'venta';
    protected $fillable = [
        'total', 'fechaVenta', 'idUsuario', 'cliente', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fechaVenta' => 'datetime',
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
