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

    /**
     * Determina si la tarea está vencida (fecha de entrega anterior a hoy y no completada).
     */
    public function getEsVencidaAttribute(): bool
    {
        return $this->fecha_entrega !== null
            && $this->fecha_entrega->startOfDay()->isBefore(now()->startOfDay())
            && $this->estado !== 'completada';
    }

    /**
     * Determina si la tarea vence pronto (en los próximos 3 días y no completada).
     */
    public function getEsProximaVencerAttribute(): bool
    {
        if ($this->fecha_entrega === null || $this->estado === 'completada' || $this->es_vencida) {
            return false;
        }

        return $this->fecha_entrega->startOfDay()->gte(now()->startOfDay())
            && $this->fecha_entrega->startOfDay()->lte(now()->addDays(3)->endOfDay());
    }

    /**
     * Texto descriptivo del tiempo restante o vencimiento.
     */
    public function getDiasRestantesTextoAttribute(): string
    {
        if ($this->estado === 'completada') {
            return 'Completada';
        }

        if (!$this->fecha_entrega) {
            return 'Sin fecha';
        }

        $diff = (int) now()->startOfDay()->diffInDays($this->fecha_entrega->startOfDay(), false);

        if ($diff < 0) {
            $dias = abs($diff);
            return $dias === 1 ? 'Venció ayer' : "Vencida hace {$dias} días";
        } elseif ($diff === 0) {
            return 'Vence hoy';
        } elseif ($diff === 1) {
            return 'Vence mañana';
        } else {
            return "Vence en {$diff} días";
        }
    }

    /**
     * Scope para tareas vencidas.
     */
    public function scopeVencidas($query)
    {
        return $query->whereNotNull('fecha_entrega')
            ->where('fecha_entrega', '<', now()->startOfDay())
            ->where('estado', '!=', 'completada');
    }

    /**
     * Scope para tareas próximas a vencer (por defecto dentro de 3 días).
     */
    public function scopeProximasVencer($query, $dias = 3)
    {
        return $query->whereNotNull('fecha_entrega')
            ->whereBetween('fecha_entrega', [now()->startOfDay(), now()->addDays($dias)->endOfDay()])
            ->where('estado', '!=', 'completada');
    }
}
