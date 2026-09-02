<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoInvestigacion extends Model
{
    protected $table = 'proyectoinvestigacion';

    protected $fillable = [
        'nombre', 'descripcion', 'objetivos', 'justificacion',
        'marcoTeorico', 'instructores', 'porcentajeAvance',
        'estado', 'fecha_inicio', 'fecha_fin', 'responsable_id', 'imagen',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio'    => 'date',
            'fecha_fin'       => 'date',
            'porcentajeAvance'=> 'float',
        ];
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class, 'proyecto_id');
    }
}
