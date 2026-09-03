<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    private function soloSuperadmin(): void
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = LogAuditoria::with('usuario')->orderByDesc('fechaHora');

        // Filtro usuario
        if ($userId = $request->input('usuario_id')) {
            $query->where('idUsuario', $userId);
        }

        // Filtro módulo
        if ($modulo = $request->input('modulo')) {
            $query->where('modulo', $modulo);
        }

        // Filtro acción
        if ($accion = $request->input('accion')) {
            $query->where('accion', $accion);
        }

        // Filtro estado
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        // Filtro fecha desde
        if ($desde = $request->input('desde')) {
            $query->whereDate('fechaHora', '>=', $desde);
        }

        // Filtro fecha hasta
        if ($hasta = $request->input('hasta')) {
            $query->whereDate('fechaHora', '<=', $hasta);
        }

        // Búsqueda texto libre
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcionOperacion', 'like', "%{$search}%")
                  ->orWhere('modulo', 'like', "%{$search}%")
                  ->orWhere('accion', 'like', "%{$search}%");
            });
        }

        $logs     = $query->paginate(20)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        // Opciones únicas para filtros
        $modulos  = LogAuditoria::whereNotNull('modulo')->distinct()->pluck('modulo')->sort()->values();
        $acciones = LogAuditoria::whereNotNull('accion')->distinct()->pluck('accion')->sort()->values();

        // KPIs rápidos del día
        $hoy         = now()->startOfDay();
        $kpiHoy      = LogAuditoria::where('fechaHora', '>=', $hoy)->count();
        $kpiFallidos = LogAuditoria::where('fechaHora', '>=', $hoy)->where('estado', 'fallido')->count();
        $kpiTotal    = LogAuditoria::count();
        $kpiUsuarios = LogAuditoria::where('fechaHora', '>=', $hoy)->distinct('idUsuario')->count('idUsuario');

        return view('superadmin.auditoria.index', compact(
            'logs', 'usuarios', 'modulos', 'acciones',
            'kpiHoy', 'kpiFallidos', 'kpiTotal', 'kpiUsuarios'
        ));
    }
}
