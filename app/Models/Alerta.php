<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alerta';

    protected $fillable = [
        'tipo',
        'mensaje',
        'fechaGeneracion',
        'leida',
        'idProducto',
    ];

    protected function casts(): array
    {
        return [
            'fechaGeneracion' => 'datetime',
            'leida'           => 'boolean',
        ];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }
}

