<?php

namespace App\Services;

use App\Mail\TareasRequerimientosPendientesMail;
use App\Models\Actividad;
use App\Models\LogAuditoria;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificacionPendientesService
{
    /**
     * Envía notificación por correo a un usuario específico.
     */
    public static function notificarUsuario(User $user, bool $esConsolidado = false): array
    {
        if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok'      => false,
                'mensaje' => "El usuario {$user->name} no tiene un correo válido registrado.",
            ];
        }

        if ($esConsolidado) {
            $tareas = Tarea::with(['responsable', 'proyecto'])
                ->where('estado', '!=', 'completada')
                ->orderByRaw('ISNULL(fecha_entrega), fecha_entrega ASC')
                ->get();

            $actividades = Actividad::with(['proyecto', 'responsableUser'])
                ->where('estado', '!=', 'finalizada')
                ->where('estado', '!=', 'completada')
                ->orderByRaw('ISNULL(fecha_limite), fecha_limite ASC')
                ->get();
        } else {
            $tareas = Tarea::with('proyecto')
                ->where('responsable_id', $user->id)
                ->where('estado', '!=', 'completada')
                ->orderByRaw('ISNULL(fecha_entrega), fecha_entrega ASC')
                ->get();

            $actividades = Actividad::with('proyecto')
                ->where(function ($q) use ($user) {
                    $q->where('responsable_id', $user->id)
                      ->orWhere('responsable', $user->name);
                })
                ->where('estado', '!=', 'finalizada')
                ->where('estado', '!=', 'completada')
                ->orderByRaw('ISNULL(fecha_limite), fecha_limite ASC')
                ->get();
        }

        if ($tareas->isEmpty() && $actividades->isEmpty()) {
            return [
                'ok'           => false,
                'mensaje'      => "No hay tareas ni requerimientos pendientes para {$user->name}.",
                'tareas'       => 0,
                'actividades'  => 0,
            ];
        }

        try {
            Mail::to($user->email)->send(new TareasRequerimientosPendientesMail($user, $tareas, $actividades, $esConsolidado));

            LogAuditoria::registrar(
                'Tareas y Requerimientos',
                'Notificación Correo',
                "Se envió notificación por correo a {$user->name} ({$user->email}) sobre {$tareas->count()} tareas y {$actividades->count()} requerimientos pendientes."
            );

            return [
                'ok'          => true,
                'mensaje'     => "Notificación enviada con éxito a {$user->email}.",
                'tareas'      => $tareas->count(),
                'actividades' => $actividades->count(),
            ];
        } catch (\Throwable $e) {
            Log::error("Error enviando notificación a {$user->email}: " . $e->getMessage());
            return [
                'ok'      => false,
                'mensaje' => "Error al enviar correo a {$user->email}: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Notifica a todos los usuarios que tengan tareas/requerimientos pendientes,
     * y envía un resumen consolidado a los Superadministradores.
     */
    public static function notificarTodos(): array
    {
        $enviados = 0;
        $detalles = [];

        // 1. Notificar a cada responsable con tareas pendientes
        $responsablesIds = Tarea::where('estado', '!=', 'completada')
            ->whereNotNull('responsable_id')
            ->distinct()
            ->pluck('responsable_id');

        $responsablesActIds = Actividad::where('estado', '!=', 'finalizada')
            ->where('estado', '!=', 'completada')
            ->whereNotNull('responsable_id')
            ->distinct()
            ->pluck('responsable_id');

        $todosIds = $responsablesIds->merge($responsablesActIds)->unique();
        $usuarios = User::whereIn('id', $todosIds)->where('activo', true)->get();

        foreach ($usuarios as $usuario) {
            $res = static::notificarUsuario($usuario, false);
            if ($res['ok'] ?? false) {
                $enviados++;
                $detalles[] = "{$usuario->name} ({$res['tareas']} tareas, {$res['actividades']} req.)";
            }
        }

        // 2. Notificar a los Superadministradores con el consolidado global
        $superadmins = User::where('role_id', 1)->where('activo', true)->get();
        foreach ($superadmins as $admin) {
            $resAdmin = static::notificarUsuario($admin, true);
            if ($resAdmin['ok'] ?? false) {
                $enviados++;
                $detalles[] = "Consolidado a Administrador: {$admin->name}";
            }
        }

        return [
            'total_enviados' => $enviados,
            'detalles'       => $detalles,
        ];
    }
}

