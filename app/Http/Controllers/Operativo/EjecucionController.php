<?php

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\ProyectoInvestigacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class EjecucionController extends Controller
{
    private function soloOperativo()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloOperativo();

        $query = Tarea::with(['responsable', 'proyecto']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhereHas('proyecto', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($proyectoId = $request->input('proyecto_id')) {
            $query->where('proyecto_id', $proyectoId);
        }

        if ($prioridad = $request->input('prioridad')) {
            $query->where('prioridad', $prioridad);
        }

        // Filtro rápido por alertas
        if ($alerta = $request->input('alerta')) {
            if ($alerta === 'vencidas') {
                $query->vencidas();
            } elseif ($alerta === 'proximas') {
                $query->proximasVencer(3);
            }
        }

        $tareas = $query->latest()->get();

        $columnas = [
            'por_iniciar' => $tareas->where('estado', 'por_iniciar')->values(),
            'en_progreso' => $tareas->where('estado', 'en_progreso')->values(),
            'completada'  => $tareas->where('estado', 'completada')->values(),
        ];

        $vencidasCount = Tarea::vencidas()->count();
        $proximasCount = Tarea::proximasVencer(3)->count();

        $stats = [
            'total'          => Tarea::count(),
            'completadas'    => Tarea::where('estado', 'completada')->count(),
            'en_progreso'    => Tarea::where('estado', 'en_progreso')->count(),
            'pendientes'     => Tarea::where('estado', 'por_iniciar')->count(),
            'vencidas'       => $vencidasCount,
            'proximas'       => $proximasCount,
        ];

        // Colecciones para el Centro de Alertas
        $alertasVencidas = Tarea::with(['responsable', 'proyecto'])->vencidas()->orderBy('fecha_entrega')->get();
        $alertasProximas = Tarea::with(['responsable', 'proyecto'])->proximasVencer(3)->orderBy('fecha_entrega')->get();

        $proyectos = ProyectoInvestigacion::orderBy('nombre')->get();
        $usuarios  = User::where('activo', true)->orderBy('name')->get();

        return view('operativo.ejecucion.index', compact(
            'columnas', 'stats', 'proyectos', 'usuarios', 'alertasVencidas', 'alertasProximas'
        ));
    }

    public function alertas()
    {
        $this->soloOperativo();

        $vencidas = Tarea::with(['responsable', 'proyecto'])->vencidas()->orderBy('fecha_entrega')->get();
        $proximas = Tarea::with(['responsable', 'proyecto'])->proximasVencer(3)->orderBy('fecha_entrega')->get();

        return response()->json([
            'success'       => true,
            'vencidas'      => $vencidas,
            'proximas'      => $proximas,
            'total_alertas' => $vencidas->count() + $proximas->count(),
        ]);
    }

    public function notificarPendientes(Request $request)
    {
        $this->soloOperativo();

        $destinatarioTipo = $request->input('destinatario', 'yo');

        if ($destinatarioTipo === 'yo') {
            $res = \App\Services\NotificacionPendientesService::notificarUsuario(auth()->user(), false);
            if ($res['ok'] ?? false) {
                return back()->with('success', $res['mensaje']);
            }
            return back()->with('info', $res['mensaje'] ?? 'No se pudo enviar el correo.');
        }

        $res = \App\Services\NotificacionPendientesService::notificarTodos();
        $total = $res['total_enviados'] ?? 0;

        if ($total === 0) {
            return back()->with('info', 'No se encontraron tareas ni requerimientos pendientes para notificar.');
        }

        return back()->with('success', "Se enviaron con éxito {$total} correos de notificación sobre tareas y requerimientos pendientes.");
    }

    public function store(Request $request)
    {
        $this->soloOperativo();

        $data = $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string|max:1000',
            'responsable_id' => 'nullable|exists:users,id',
            'fecha_entrega'  => 'nullable|date',
            'estado'         => 'required|in:por_iniciar,en_progreso,completada',
            'prioridad'      => 'required|in:alta,media,baja',
            'proyecto_id'    => 'nullable|exists:proyectoinvestigacion,id',
        ]);

        $tarea = Tarea::create($data);
        LogAuditoria::registrar('Tareas', 'Creación', "Se creó la tarea: {$data['nombre']}");

        return response()->json(['ok' => true, 'tarea' => $tarea->load(['responsable', 'proyecto'])]);
    }

    public function show(Tarea $tarea)
    {
        $this->soloOperativo();
        return response()->json($tarea->load(['responsable', 'proyecto']));
    }

    public function update(Request $request, Tarea $tarea)
    {
        $this->soloOperativo();

        $data = $request->validate([
            'nombre'         => 'required|string|max:255',
            'descripcion'    => 'nullable|string|max:1000',
            'responsable_id' => 'nullable|exists:users,id',
            'fecha_entrega'  => 'nullable|date',
            'estado'         => 'required|in:por_iniciar,en_progreso,completada',
            'prioridad'      => 'required|in:alta,media,baja',
            'proyecto_id'    => 'nullable|exists:proyectoinvestigacion,id',
        ]);

        $tarea->update($data);

        return response()->json(['ok' => true, 'tarea' => $tarea->load(['responsable', 'proyecto'])]);
    }

    public function moverEstado(Request $request, Tarea $tarea)
    {
        $this->soloOperativo();
        $request->validate(['estado' => 'required|in:por_iniciar,en_progreso,completada']);
        $tarea->update(['estado' => $request->estado]);
        return response()->json(['ok' => true]);
    }

    public function destroy(Tarea $tarea)
    {
        $this->soloOperativo();
        $tarea->delete();
        return response()->json(['ok' => true]);
    }
}
