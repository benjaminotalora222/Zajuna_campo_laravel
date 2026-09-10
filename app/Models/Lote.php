<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table    = 'lotes';
    protected $fillable = [
        'producto_id', 'cantidad_inicial', 'cantidad_disponible',
        'fecha_entrada', 'fecha_vencimiento', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_entrada'      => 'date',
            'fecha_vencimiento'  => 'date',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'lote_id');
    }

    /**
     * Indica si el lote está próximo a vencer (dentro de 30 días).
     */
    public function getProximoVencerAttribute(): bool
    {
        return $this->fecha_vencimiento
            && $this->fecha_vencimiento->isFuture()
            && $this->fecha_vencimiento->diffInDays(now()) <= 30;
    }

    /**
     * Indica si el lote ya venció.
     */
    public function getVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }
}
