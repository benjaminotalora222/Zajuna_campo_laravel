<?php

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Evento;
use Illuminate\Http\Request;

class CronogramaController extends Controller
{
    private function soloOperativo()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloOperativo();

        $anio = (int) $request->input('anio', now()->year);
        $mes  = (int) $request->input('mes',  now()->month);

        if ($mes < 1)  { $mes = 12; $anio--; }
        if ($mes > 12) { $mes = 1;  $anio++; }

        $inicio = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        $filtroTipo = $request->input('tipo', '');

        $query = Evento::whereYear('fecha', $anio)->whereMonth('fecha', $mes);
        if ($filtroTipo) {
            $query->where('tipo', $filtroTipo);
        }

        $eventos = $query->orderBy('fecha')->orderBy('hora_inicio')->get();
        $eventosPorDia = $eventos->groupBy(fn($e) => $e->fecha->day);

        return view('operativo.cronograma.index', compact(
            'eventos', 'eventosPorDia', 'anio', 'mes', 'inicio', 'fin', 'filtroTipo'
        ));
    }

    public function store(Request $request)
    {
        $this->soloOperativo();

        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:visita_tecnica,capacitacion,reunion,otro',
            'espacio'     => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i',
        ]);

        $data['creado_por'] = auth()->id();
        $evento = Evento::create($data);

        // Sincronizar y crear actividad en el módulo de Ejecución de Actividades
        $fechaHora = null;
        if (!empty($data['fecha'])) {
            $hora = !empty($data['hora_inicio']) ? $data['hora_inicio'] . ':00' : '00:00:00';
            $fechaHora = "{$data['fecha']} {$hora}";
        }

        Actividad::create([
            'tema'           => $evento->titulo,
            'descripcion'    => $evento->descripcion,
            'tipo'           => $evento->tipo,
            'espacio_fisico' => $evento->espacio,
            'fecha_hora'     => $fechaHora,
            'fecha_limite'   => $evento->fecha,
            'responsable_id' => $evento->creado_por,
            'estado'         => 'pendiente',
            'progreso'       => 0,
            'evento_id'      => $evento->id,
        ]);

        return response()->json(['ok' => true, 'message' => 'Evento creado y conectado a ejecución de actividades.']);
    }

    public function show(Evento $evento)
    {
        $this->soloOperativo();
        return response()->json($evento);
    }

    public function update(Request $request, Evento $evento)
    {
        $this->soloOperativo();

        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'tipo'        => 'required|in:visita_tecnica,capacitacion,reunion,otro',
            'espacio'     => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i',
        ]);

        $evento->update($data);

        // Actualizar actividad vinculada
        $fechaHora = null;
        if (!empty($data['fecha'])) {
            $hora = !empty($data['hora_inicio']) ? $data['hora_inicio'] . ':00' : '00:00:00';
            $fechaHora = "{$data['fecha']} {$hora}";
        }

        Actividad::updateOrCreate(
            ['evento_id' => $evento->id],
            [
                'tema'           => $evento->titulo,
                'descripcion'    => $evento->descripcion,
                'tipo'           => $evento->tipo,
                'espacio_fisico' => $evento->espacio,
                'fecha_hora'     => $fechaHora,
                'fecha_limite'   => $evento->fecha,
                'responsable_id' => $evento->creado_por ?? auth()->id(),
            ]
        );

        return response()->json(['ok' => true, 'message' => 'Evento actualizado.']);
    }

    public function destroy(Evento $evento)
    {
        $this->soloOperativo();
        Actividad::where('evento_id', $evento->id)->delete();
        $evento->delete();
        return response()->json(['ok' => true, 'message' => 'Evento eliminado.']);
    }
}
