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

        $operativos = \App\Models\User::where('role_id', 2)->orderBy('name')->get();

        return view('superadmin.configuracion.index', compact('config', 'zonas', 'idiomas', 'operativos'));
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
            'modulo_ventas_operativo',
        ];

        // Los toggles que no vengan en el request se guardan como '0'
        $toggles = [
            'notif_correo', 'notif_recordatorios', 'notif_alertas_criticas',
            'seg_dos_pasos', 'seg_sesion_automatica',
            'modulo_ventas_operativo',
        ];

        foreach ($campos as $clave) {
            if (in_array($clave, $toggles)) {
                ConfiguracionSistema::set($clave, $request->has($clave) ? '1' : '0');
            } else {
                ConfiguracionSistema::set($clave, $request->input($clave));
            }
        }

        // Actualizar permisos individuales de usuarios operativos para ventas
        if ($request->has('operativos_ids')) {
            $operativosIds = $request->input('operativos_ids', []);
            $ventasPermitidas = $request->input('operativos_ventas', []);

            foreach ($operativosIds as $opId) {
                $acceso = isset($ventasPermitidas[$opId]);
                \App\Models\User::where('id', $opId)
                    ->where('role_id', 2)
                    ->update(['acceso_ventas' => $acceso]);
            }
        }

        LogAuditoria::registrar('Configuración', 'Actualización', 'Se actualizó la configuración del sistema.');

        return redirect()->route('superadmin.configuracion.index')
                         ->with('success', 'Configuración guardada correctamente.');
    }

    /**
     * Alternar rápidamente el bloqueo/habilitación del módulo de ventas para el rol operativo.
     */
    public function toggleVentasOperativo(Request $request)
    {
        $this->soloSuperadmin();

        $algunoHabilitado = \App\Models\User::where('role_id', 2)->where('acceso_ventas', true)->exists();
        $nuevoEstado = !$algunoHabilitado;

        \App\Models\User::where('role_id', 2)->update(['acceso_ventas' => $nuevoEstado]);
        ConfiguracionSistema::set('modulo_ventas_operativo', $nuevoEstado ? '1' : '0');

        $accion = $nuevoEstado ? 'habilitó a todos los operativos' : 'bloqueó a todos los operativos';
        LogAuditoria::registrar('Permisos', 'Actualización', "El superadministrador {$accion} en el módulo de ventas.");

        $mensaje = $nuevoEstado
            ? 'Módulo de ventas HABILITADO para todos los usuarios operativos.'
            : 'Módulo de ventas BLOQUEADO para todos los usuarios operativos.';

        return back()->with('success', $mensaje);
    }
}
