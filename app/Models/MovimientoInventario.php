<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table    = 'movimientoinventario';
    protected $fillable = [
        'producto_id', 'lote_id', 'user_id',
        'tipo',       // 'entrada' | 'salida'
        'cantidad',
        'motivo',
        'fechaMovimiento',
    ];

    protected function casts(): array
    {
        return [
            'fechaMovimiento' => 'datetime',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
