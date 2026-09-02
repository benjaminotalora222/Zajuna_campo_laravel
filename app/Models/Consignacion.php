<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consignacion extends Model
{
    protected $table = 'consignacion';

    protected $fillable = [
        'proveedor_id', 'nombre_producto', 'sku', 'unidad',
        'stock_disponible', 'fecha_vencimiento', 'estado', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
        ];
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Recalcula y guarda el estado según la fecha de vencimiento y el stock.
     */
    public function recalcularEstado(): void
    {
        if ($this->stock_disponible <= 0) {
            $this->estado = 'agotado';
        } elseif ($this->fecha_vencimiento && $this->fecha_vencimiento->diffInDays(now()) <= 30 && $this->fecha_vencimiento->isFuture()) {
            $this->estado = 'proximo_vencer';
        } else {
            $this->estado = 'disponible';
        }
    }
}
