<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\Request;

class CronogramaController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        // Mes y año activos
        $anio = (int) $request->input('anio', now()->year);
        $mes  = (int) $request->input('mes',  now()->month);

        // Clamp
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

        // Agrupar por día para el calendario
        $eventosPorDia = $eventos->groupBy(fn($e) => $e->fecha->day);

        return view('superadmin.cronograma.index', compact(
            'eventos', 'eventosPorDia', 'anio', 'mes', 'inicio', 'fin', 'filtroTipo'
        ));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

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
        Evento::create($data);

        return response()->json(['ok' => true, 'message' => 'Evento creado.']);
    }

    public function show(Evento $evento)
    {
        $this->soloSuperadmin();
        return response()->json($evento);
    }

    public function update(Request $request, Evento $evento)
    {
        $this->soloSuperadmin();

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

        return response()->json(['ok' => true, 'message' => 'Evento actualizado.']);
    }

    public function destroy(Evento $evento)
    {
        $this->soloSuperadmin();
        $evento->delete();
        return response()->json(['ok' => true, 'message' => 'Evento eliminado.']);
    }
}
