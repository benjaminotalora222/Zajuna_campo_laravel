<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    private function soloSuperadmin(): void
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    public function index()
    {
        $this->soloSuperadmin();

        $config = ConfiguracionSistema::pluck('valor', 'clave');

        $zonas = [
            'America/Bogota'      => '(GMT-05:00) Bogotá, Lima, Quito',
            'America/Mexico_City' => '(GMT-06:00) Ciudad de México',
            'America/New_York'    => '(GMT-05:00) Nueva York',
            'Europe/Madrid'       => '(GMT+01:00) Madrid',
            'UTC'                 => '(UTC) Coordinado Universal',
        ];

        $idiomas = [
            'es' => 'Español',
            'en' => 'English',
        ];

        return view('superadmin.configuracion.index', compact('config', 'zonas', 'idiomas'));
    }

    public function update(Request $request)
    {
        $this->soloSuperadmin();

        $request->validate([
            'nombre_plataforma' => 'required|string|max:100',
            'zona_horaria'      => 'required|string|max:100',
            'idioma'            => 'required|string|max:10',
        ]);

        $campos = [
            'nombre_plataforma',
            'zona_horaria',
            'idioma',
            'notif_correo',
            'notif_recordatorios',
            'notif_alertas_criticas',
            'seg_dos_pasos',
            'seg_sesion_automatica',
        ];

        // Los toggles que no vengan en el request se guardan como '0'
        $toggles = [
            'notif_correo', 'notif_recordatorios', 'notif_alertas_criticas',
            'seg_dos_pasos', 'seg_sesion_automatica',
        ];

        foreach ($campos as $clave) {
            if (in_array($clave, $toggles)) {
                ConfiguracionSistema::set($clave, $request->has($clave) ? '1' : '0');
            } else {
                ConfiguracionSistema::set($clave, $request->input($clave));
            }
        }

        LogAuditoria::registrar('Configuración', 'Actualización', 'Se actualizó la configuración del sistema.');

        return redirect()->route('superadmin.configuracion.index')
                         ->with('success', 'Configuración guardada correctamente.');
    }
}
