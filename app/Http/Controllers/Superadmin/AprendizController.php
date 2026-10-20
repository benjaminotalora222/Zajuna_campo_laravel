<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Aprendiz;
use App\Models\LogAuditoria;
use App\Models\ProyectoInvestigacion;
use Illuminate\Http\Request;

class AprendizController extends Controller
{
    private function verificarAcceso()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function index(Request $request)
    {
        $this->verificarAcceso();

        $query = Aprendiz::with(['proyecto', 'registrador'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%")
                  ->orWhere('correo_electronico', 'like', "%{$search}%")
                  ->orWhere('ficha_programa', 'like', "%{$search}%")
                  ->orWhere('programa_formacion', 'like', "%{$search}%")
                  ->orWhere('semillero', 'like', "%{$search}%")
                  ->orWhere('nombre_proyecto', 'like', "%{$search}%");
            });
        }

        if ($semillero = $request->input('semillero')) {
            $query->where('semillero', $semillero);
        }

        if ($tipo = $request->input('tipo_programa')) {
            $query->where('tipo_programa', $tipo);
        }

        $aprendices = $query->paginate(15)->withQueryString();
        $semilleros = ['SIPPA', 'SEBI', 'INNOVACIÓN AGROINDUSTRIAL', 'FISENSO', 'AGRÍCOLA', 'AGROGESTIÓN', 'IDEAR', 'TEDAF'];

        return view('superadmin.proyectos.aprendices', compact('aprendices', 'semilleros'));
    }

    public function store(Request $request)
    {
        $this->verificarAcceso();

        $data = $request->validate([
            // 1. NOMBRE COMPLETO *
            'nombre_completo'           => 'required|string|max:255',
            // 2. TIPO DE DOCUMENTO *
            'tipo_documento'            => 'required|in:T.I,CC,OTRO',
            'tipo_documento_otro'       => 'nullable|string|max:100',
            // 3. NÚMERO DE DOCUMENTO *
            'numero_documento'          => ['required', 'regex:/^[0-9]+$/', 'max:50'],
            // 4. GÉNERO *
            'genero'                    => 'required|in:FEMENINO,MASCULINO,OTRO',
            // 5. RH *
            'rh'                        => 'required|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            // 6. EPS *
            'eps'                       => 'required|string|max:150',
            // 7. TELÉFONO *
            'telefono'                  => ['required', 'regex:/^[0-9]{10}$/'],
            // 8. CORREO ELECTRÓNICO *
            'correo_electronico'        => 'required|email|max:255',
            // 9. TIPO DE PROGRAMA *
            'tipo_programa'             => 'required|in:Tecnólogo,Técnico,Complementaria',
            // 10. PROGRAMA DE FORMACIÓN *
            'programa_formacion'        => 'required|string|max:255',
            'programa_formacion_otro'   => 'nullable|required_if:programa_formacion,OTRO|string|max:255',
            // 11. FICHA DEL PROGRAMA DE FORMACIÓN *
            'ficha_programa'            => ['required', 'regex:/^[0-9]+$/', 'max:50'],
            // 12. ¿Cuenta con tipo de auxilio o de apoyo económico por parte del SENA?
            'apoyo_sena'                => 'nullable|in:APOYO Y SOSTENIMIENTO,CONTRATO DE APRENDIZAJE ETAPA LECTIVA,CENTRO DE CONVIVENCIA,APOYO ALIMENTACIÓN,OTRO,NINGUNO',
            'apoyo_sena_otro'           => 'nullable|required_if:apoyo_sena,OTRO|string|max:255',
            // 13. ¿A QUÉ SEMILLERO DE INVESTIGACIÓN DESEA VINCULARSE? *
            'semillero'                 => 'required|in:SIPPA,SEBI,INNOVACIÓN AGROINDUSTRIAL,FISENSO,AGRÍCOLA,AGROGESTIÓN,IDEAR,TEDAF',
            // 14. NOMBRE DEL PROYECTO DE INVESTIGACIÓN EN EL QUE ESTÁ TRABAJANDO *
            'nombre_proyecto'           => 'required|string|max:2000',
            // 15. NOMBRE DEL INSTRUCTOR LÍDER Y ASESORES DEL PROYECTO DE INVESTIGACIÓN *
            'instructor_lider_asesores' => 'required|string|max:2000',
            // Extras
            'proyecto_id'               => 'nullable|exists:proyectoinvestigacion,id',
        ], [
            'nombre_completo.required'           => 'El nombre completo es obligatorio.',
            'tipo_documento.required'            => 'Seleccione el tipo de documento.',
            'numero_documento.required'          => 'El número de documento es obligatorio.',
            'numero_documento.regex'             => 'El documento debe contener únicamente números, sin puntos, comas ni letras.',
            'genero.required'                    => 'Seleccione el género.',
            'rh.required'                        => 'Seleccione el grupo sanguíneo y factor RH.',
            'eps.required'                       => 'La EPS es obligatoria.',
            'telefono.required'                  => 'El teléfono es obligatorio.',
            'telefono.regex'                     => 'El teléfono debe contener exactamente 10 dígitos numéricos.',
            'correo_electronico.required'        => 'El correo electrónico es obligatorio.',
            'correo_electronico.email'           => 'El correo electrónico debe ser una dirección válida.',
            'tipo_programa.required'             => 'Seleccione el tipo de programa.',
            'programa_formacion.required'        => 'Seleccione el programa de formación.',
            'programa_formacion_otro.required_if'=> 'Especifique cuál es el programa de formación si seleccionó OTRO.',
            'ficha_programa.required'            => 'La ficha del programa es obligatoria.',
            'ficha_programa.regex'               => 'La ficha debe contener únicamente números.',
            'apoyo_sena_otro.required_if'        => 'Especifique el tipo de apoyo económico si seleccionó OTRO.',
            'semillero.required'                 => 'Seleccione el semillero de investigación al que desea vincularse.',
            'nombre_proyecto.required'           => 'El nombre del proyecto de investigación es obligatorio.',
            'instructor_lider_asesores.required' => 'El nombre del instructor líder y asesores es obligatorio.',
        ]);

        // Asegurar que nombre completo quede en MAYÚSCULAS
        $data['nombre_completo'] = mb_strtoupper(trim($data['nombre_completo']), 'UTF-8');
        $data['user_id'] = auth()->id();

        // Si se vincula con un proyecto por nombre si no viene proyecto_id
        if (empty($data['proyecto_id'])) {
            $proyectoCoincidente = ProyectoInvestigacion::where('nombre', 'like', '%' . trim($data['nombre_proyecto']) . '%')->first();
            if ($proyectoCoincidente) {
                $data['proyecto_id'] = $proyectoCoincidente->id;
            }
        }

        $aprendiz = Aprendiz::create($data);

        LogAuditoria::registrar(
            'Proyectos',
            'Registro Aprendiz',
            "Se registró al aprendiz: {$aprendiz->nombre_completo} (Doc: {$aprendiz->numero_documento}) en el semillero {$aprendiz->semillero}"
        );

        return redirect()->back()->with('success', "¡Aprendiz {$aprendiz->nombre_completo} registrado exitosamente!");
    }

    public function destroy(Aprendiz $aprendiz)
    {
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Solo el Superadministrador puede eliminar registros de aprendices.');
        }

        $nombre = $aprendiz->nombre_completo;
        $aprendiz->delete();

        LogAuditoria::registrar(
            'Proyectos',
            'Eliminación Aprendiz',
            "Se eliminó el registro del aprendiz: {$nombre}"
        );

        return redirect()->back()->with('success', "Registro del aprendiz {$nombre} eliminado correctamente.");
    }
}
