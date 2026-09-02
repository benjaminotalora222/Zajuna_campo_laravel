<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    protected $table = 'tarea';

    protected $fillable = [
        'nombre', 'descripcion', 'responsable_id',
        'fecha_entrega', 'estado', 'prioridad', 'proyecto_id',
    ];

    protected function casts(): array
    {
        return ['fecha_entrega' => 'date'];
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function proyecto()
    {
        return $this->belongsTo(ProyectoInvestigacion::class, 'proyecto_id');
    }
}
