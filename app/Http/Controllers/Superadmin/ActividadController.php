<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\ProyectoInvestigacion;
use App\Models\User;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = Actividad::with(['proyecto', 'responsableUser'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('tema', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('proyecto', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($proyectoId = $request->input('proyecto_id')) {
            $query->where('proyecto_id', $proyectoId);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($fecha = $request->input('fecha')) {
            $query->whereDate('fecha_limite', $fecha);
        }

        $actividades = $query->paginate(15)->withQueryString();
        $proyectos   = ProyectoInvestigacion::orderBy('nombre')->get();
        $usuarios    = User::where('activo', true)->orderBy('name')->get();

        return view('superadmin.actividades.index', compact('actividades', 'proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'tema'           => 'required|string|max:255',
            'descripcion'    => 'nullable|string|max:1000',
            'tipo'           => 'nullable|string|max:100',
            'proyecto_id'    => 'nullable|exists:proyectoinvestigacion,id',
            'responsable_id' => 'nullable|exists:users,id',
            'progreso'       => 'nullable|integer|min:0|max:100',
            'estado'         => 'required|in:pendiente,en_ejecucion,finalizado,pausado',
            'fecha_limite'   => 'nullable|date',
            'fecha_hora'     => 'nullable|date',
        ]);

        Actividad::create($data);

        return redirect()->route('superadmin.actividades.index')
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function update(Request $request, Actividad $actividad)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'tema'           => 'required|string|max:255',
            'descripcion'    => 'nullable|string|max:1000',
            'tipo'           => 'nullable|string|max:100',
            'proyecto_id'    => 'nullable|exists:proyectoinvestigacion,id',
            'responsable_id' => 'nullable|exists:users,id',
            'progreso'       => 'nullable|integer|min:0|max:100',
            'estado'         => 'required|in:pendiente,en_ejecucion,finalizado,pausado',
            'fecha_limite'   => 'nullable|date',
            'fecha_hora'     => 'nullable|date',
        ]);

        $actividad->update($data);

        return redirect()->route('superadmin.actividades.index')
                         ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $this->soloSuperadmin();
        $actividad->delete();

        return redirect()->route('superadmin.actividades.index')
                         ->with('success', 'Actividad eliminada.');
    }
}
