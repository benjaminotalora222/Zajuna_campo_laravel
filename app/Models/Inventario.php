<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'Inventario';
    protected $primaryKey = 'idInventario';
    public $timestamps = false;

    protected $fillable = [
        'idProducto', 'cantidadActual', 'ubicacion', 'fechaActualizacion',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }
}