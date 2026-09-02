<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table    = 'producto';
    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'unidad',
        'stockActual', 'stockMinimo', 'categoria',
        'codigoBarras', 'diasPerecederoMax', 'imagen', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo'  => 'boolean',
            'precio'  => 'decimal:2',
        ];
    }

    public function getStockEstadoAttribute(): string
    {
        if ($this->stockActual === null) return 'sin_datos';
        if ($this->stockActual <= 0)    return 'agotado';
        if ($this->stockMinimo !== null && $this->stockActual <= $this->stockMinimo) return 'bajo';
        return 'ok';
    }
}
