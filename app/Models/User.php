<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'activo', 'proveedor_id', 'acceso_ventas'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
            'acceso_ventas'     => 'boolean',
        ];
    }

    /**
     * Comprueba si el usuario tiene permiso para acceder al módulo de ventas.
     */
    public function tieneAccesoVentas(): bool
    {
        // El superadmin (rol 1) siempre tiene acceso completo
        if ((int) $this->role_id === 1) {
            return true;
        }

        // Si no es operativo (rol 2), no tiene acceso al módulo operativo de ventas
        if ((int) $this->role_id !== 2) {
            return false;
        }

        // Permiso individual del usuario operativo
        return (bool) ($this->acceso_ventas ?? true);
    }

    /**
     * Relación con el modelo Rol.
     */
    public function rol()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    /**
     * Relación con el proveedor asociado (solo para usuarios con role_id = 3).
     */
    public function proveedor()
    {
        return $this->belongsTo(\App\Models\Proveedor::class, 'proveedor_id');
    }
}
