<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\ProyectoInvestigacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProyectoController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = ProyectoInvestigacion::with(['responsable', 'aprendices']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        $orden = $request->input('orden', 'recientes');
        match ($orden) {
            'avance_desc' => $query->orderByDesc('porcentajeAvance'),
            'avance_asc'  => $query->orderBy('porcentajeAvance'),
            'nombre'      => $query->orderBy('nombre'),
            default       => $query->latest(),
        };

        $proyectos = $query->get();
        $usuarios  = User::where('activo', true)->orderBy('name')->get();

        $total = $proyectos->count();
        $aprendices = \App\Models\Aprendiz::with('proyecto')->latest()->get();
        $totalAprendices = $aprendices->count();

        return view('superadmin.proyectos.index', compact('proyectos', 'usuarios', 'total', 'aprendices', 'totalAprendices'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string|max:1000',
            'objetivos'       => 'nullable|string',
            'estado'          => 'required|in:en_progreso,planificado,completado,pausado',
            'fecha_inicio'    => 'nullable|date',
            'fecha_fin'       => 'nullable|date',
            'responsable_id'  => 'nullable|exists:users,id',
            'porcentajeAvance'=> 'nullable|numeric|min:0|max:100',
            'imagen'          => 'nullable|image|max:2048',
            'aprendices'      => 'nullable|array',
            'aprendices.*'    => 'exists:aprendices,id',
        ]);

        $aprendicesIds = $request->input('aprendices', []);
        unset($data['aprendices']);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('proyectos', 'public');
        }

        $proyecto = ProyectoInvestigacion::create($data);

        if (!empty($aprendicesIds)) {
            $proyecto->aprendices()->sync($aprendicesIds);
            \App\Models\Aprendiz::whereIn('id', $aprendicesIds)->update([
                'proyecto_id'     => $proyecto->id,
                'nombre_proyecto' => $proyecto->nombre,
            ]);
        }

        LogAuditoria::registrar('Proyectos', 'Creación', "Se creó el proyecto: {$data['nombre']}");

        return redirect()->route('superadmin.proyectos.index')
                         ->with('success', 'Proyecto creado correctamente.');
    }

    public function update(Request $request, ProyectoInvestigacion $proyecto)
    {
        $this->soloSuperadmin();

        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string|max:1000',
            'objetivos'       => 'nullable|string',
            'estado'          => 'required|in:en_progreso,planificado,completado,pausado',
            'fecha_inicio'    => 'nullable|date',
            'fecha_fin'       => 'nullable|date',
            'responsable_id'  => 'nullable|exists:users,id',
            'porcentajeAvance'=> 'nullable|numeric|min:0|max:100',
            'imagen'          => 'nullable|image|max:2048',
            'aprendices'      => 'nullable|array',
            'aprendices.*'    => 'exists:aprendices,id',
        ]);

        $aprendicesIds = $request->input('aprendices', []);
        unset($data['aprendices']);

        if ($request->hasFile('imagen')) {
            if ($proyecto->imagen) Storage::disk('public')->delete($proyecto->imagen);
            $data['imagen'] = $request->file('imagen')->store('proyectos', 'public');
        }

        $proyecto->update($data);

        $proyecto->aprendices()->sync($aprendicesIds);
        if (!empty($aprendicesIds)) {
            \App\Models\Aprendiz::whereIn('id', $aprendicesIds)->update([
                'proyecto_id'     => $proyecto->id,
                'nombre_proyecto' => $proyecto->nombre,
            ]);
        }

        LogAuditoria::registrar('Proyectos', 'Actualización', "Se actualizó el proyecto: {$proyecto->nombre}");

        return redirect()->route('superadmin.proyectos.index')
                         ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(ProyectoInvestigacion $proyecto)
    {
        $this->soloSuperadmin();
        if ($proyecto->imagen) Storage::disk('public')->delete($proyecto->imagen);
        $proyecto->delete();

        LogAuditoria::registrar('Proyectos', 'Eliminación', "Se eliminó el proyecto: {$proyecto->nombre}");

        return redirect()->route('superadmin.proyectos.index')
                         ->with('success', 'Proyecto eliminado.');
    }
}
