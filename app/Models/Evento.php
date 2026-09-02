<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'evento';

    protected $fillable = [
        'titulo', 'descripcion', 'tipo', 'espacio',
        'fecha', 'hora_inicio', 'hora_fin', 'creado_por',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
