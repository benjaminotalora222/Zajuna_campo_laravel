<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprendiz extends Model
{
    use HasFactory;

    protected $table = 'aprendices';

    protected $fillable = [
        'nombre_completo',
        'tipo_documento',
        'tipo_documento_otro',
        'numero_documento',
        'genero',
        'rh',
        'eps',
        'telefono',
        'correo_electronico',
        'tipo_programa',
        'programa_formacion',
        'programa_formacion_otro',
        'ficha_programa',
        'apoyo_sena',
        'apoyo_sena_otro',
        'semillero',
        'nombre_proyecto',
        'instructor_lider_asesores',
        'proyecto_id',
        'user_id',
    ];

    public function proyecto()
    {
        return $this->belongsTo(ProyectoInvestigacion::class, 'proyecto_id');
    }

    public function proyectos()
    {
        return $this->belongsToMany(ProyectoInvestigacion::class, 'aprendiz_proyecto', 'aprendiz_id', 'proyecto_id')->withTimestamps();
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getProgramaCompletoAttribute(): string
    {
        if ($this->programa_formacion === 'OTRO' && !empty($this->programa_formacion_otro)) {
            return "OTRO ({$this->programa_formacion_otro})";
        }
        return $this->programa_formacion ?? '';
    }

    public function getApoyoCompletoAttribute(): string
    {
        if ($this->apoyo_sena === 'OTRO' && !empty($this->apoyo_sena_otro)) {
            return "OTRO ({$this->apoyo_sena_otro})";
        }
        return $this->apoyo_sena ?? 'NINGUNO';
    }
}
