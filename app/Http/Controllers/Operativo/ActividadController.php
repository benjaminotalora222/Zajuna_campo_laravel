<?php

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\LogAuditoria;
use App\Models\ProyectoInvestigacion;
use App\Models\User;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    private function soloOperativo()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloOperativo();

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

        return view('operativo.actividades.index', compact('actividades', 'proyectos', 'usuarios'));
    }

    public function store(Request $request)
    {
        $this->soloOperativo();

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
        LogAuditoria::registrar('Actividades', 'Creación', "Se creó la actividad: {$data['tema']}");

        return redirect()->route('operativo.actividades.index')
                         ->with('success', 'Actividad creada correctamente.');
    }

    public function update(Request $request, Actividad $actividad)
    {
        $this->soloOperativo();

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
        LogAuditoria::registrar('Actividades', 'Actualización', "Se actualizó la actividad: {$actividad->tema}");

        return redirect()->route('operativo.actividades.index')
                         ->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Actividad $actividad)
    {
        $this->soloOperativo();
        $actividad->delete();
        LogAuditoria::registrar('Actividades', 'Eliminación', "Se eliminó la actividad: {$actividad->tema}");

        return redirect()->route('operativo.actividades.index')
                         ->with('success', 'Actividad eliminada.');
    }
}
