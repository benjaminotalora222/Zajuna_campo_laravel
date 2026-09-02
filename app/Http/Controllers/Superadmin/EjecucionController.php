<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ProyectoInvestigacion;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class EjecucionController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

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

        $tareas = $query->latest()->get();

        $columnas = [
            'por_iniciar' => $tareas->where('estado', 'por_iniciar')->values(),
            'en_progreso' => $tareas->where('estado', 'en_progreso')->values(),
            'completada'  => $tareas->where('estado', 'completada')->values(),
        ];

        $stats = [
            'total'       => $tareas->count(),
            'completadas' => $tareas->where('estado', 'completada')->count(),
            'en_progreso' => $tareas->where('estado', 'en_progreso')->count(),
            'pendientes'  => $tareas->where('estado', 'por_iniciar')->count(),
        ];

        $proyectos = ProyectoInvestigacion::orderBy('nombre')->get();
        $usuarios  = User::where('activo', true)->orderBy('name')->get();

        return view('superadmin.ejecucion.index', compact('columnas', 'stats', 'proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

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

        return response()->json(['ok' => true, 'tarea' => $tarea->load(['responsable', 'proyecto'])]);
    }

    public function show(Tarea $tarea)
    {
        $this->soloSuperadmin();
        return response()->json($tarea->load(['responsable', 'proyecto']));
    }

    public function update(Request $request, Tarea $tarea)
    {
        $this->soloSuperadmin();

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
        $this->soloSuperadmin();
        $request->validate(['estado' => 'required|in:por_iniciar,en_progreso,completada']);
        $tarea->update(['estado' => $request->estado]);
        return response()->json(['ok' => true]);
    }

    public function destroy(Tarea $tarea)
    {
        $this->soloSuperadmin();
        $tarea->delete();
        return response()->json(['ok' => true]);
    }
}
