<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAuditoria extends Model
{
    protected $table    = 'LogAuditoria';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idUsuario', 'modulo', 'accion', 'descripcionOperacion', 'fechaHora', 'estado',
    ];

    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'fechaHora' => 'datetime',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }

    /**
     * Registra una entrada en el log de auditoría.
     */
    public static function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        string $estado = 'exitoso'
    ): void {
        try {
            static::create([
                'idUsuario'            => auth()->id(),
                'modulo'               => $modulo,
                'accion'               => $accion,
                'descripcionOperacion' => $descripcion,
                'fechaHora'            => now(),
                'estado'               => $estado,
            ]);
        } catch (\Throwable) {
            // No interrumpir el flujo si el log falla
        }
    }
}
