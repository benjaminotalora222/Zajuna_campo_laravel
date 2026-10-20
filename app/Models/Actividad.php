<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';

    protected $fillable = [
        'tipo', 'tema', 'descripcion', 'progreso', 'fecha_hora',
        'duracion', 'espacio_fisico', 'responsable', 'responsable_id',
        'estado', 'fecha_limite', 'proyecto_id', 'evento_id',
        'documento', 'documento_nombre', 'documentos',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora'   => 'datetime',
            'fecha_limite' => 'date',
            'documentos'   => 'array',
        ];
    }

    public function proyecto()
    {
        return $this->belongsTo(ProyectoInvestigacion::class, 'proyecto_id');
    }

    public function responsableUser()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
