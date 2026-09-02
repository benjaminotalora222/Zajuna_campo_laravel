<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';

    protected $fillable = ['nombre', 'descripcion', 'icono', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    /** Cuenta productos que tienen esta categoría por nombre */
    public function totalProductos(): int
    {
        return Producto::where('categoria', $this->nombre)->count();
    }
}
