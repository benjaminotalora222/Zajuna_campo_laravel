<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Producto;

class InventarioPolicy
{
    /** Superadmin (role_id=1) puede todo */
    public function before(User $user): ?bool
    {
        if ($user->role_id === 1) return true;
        return null;
    }

    /** Ver listado de inventario */
    public function viewAny(User $user): bool
    {
        return in_array($user->role_id, [1, 2]); // superadmin + operativo
    }

    /** Ver detalle de un producto en inventario */
    public function view(User $user, Producto $producto): bool
    {
        return in_array($user->role_id, [1, 2]);
    }

    /** Registrar entradas/salidas (movimientos) — operativo y superadmin */
    public function registrarMovimiento(User $user, Producto $producto): bool
    {
        return in_array($user->role_id, [1, 2]);
    }

    /** Editar la ficha del producto — solo superadmin (cubierto por before()) */
    public function editarFicha(User $user, Producto $producto): bool
    {
        return false; // before() lo permite para role_id=1
    }

    /** Eliminar producto del catálogo — solo superadmin (cubierto por before()) */
    public function delete(User $user, Producto $producto): bool
    {
        return false; // before() lo permite para role_id=1
    }
}
