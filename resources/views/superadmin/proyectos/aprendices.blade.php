@php
$title = 'Aprendices Registrados';
$isSuperadmin = auth()->user()->role_id === 1;
$proyectosRoute = $isSuperadmin ? route('superadmin.proyectos.index') : route('operativo.proyectos.index');
$aprendicesRoute = $isSuperadmin ? route('superadmin.proyectos.aprendices.index') : route('operativo.proyectos.aprendices.index');
@endphp

<x-dynamic-component :component="$isSuperadmin ? 'superadmin-layout' : 'operativo-layout'" :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ $proyectosRoute }}" class="text-xs font-bold hover:underline flex items-center gap-1" style="color:#71277a;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                    Volver a Proyectos
                </a>
                <span class="text-xs text-gray-400">/</span>
                <span class="text-xs font-semibold text-gray-500">Aprendices</span>
            </div>
            <h1 class="text-2xl font-extrabold leading-tight">Aprendices Vinculados a Investigación</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Listado de aprendices SENA registrados en proyectos y semilleros de investigación.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $proyectosRoute }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border font-bold text-sm bg-white hover:bg-gray-50 transition"
               style="border-color:#e7e0cc; color:#1c2b16;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Ver Módulo Proyectos
            </a>
        </div>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl p-4 mb-6 shadow-sm border border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ $aprendicesRoute }}" class="flex items-center gap-2 flex-wrap w-full md:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, documento, ficha..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>

            <select name="semillero" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-xl border text-sm outline-none"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los semilleros</option>
                @foreach($semilleros as $s)
                    <option value="{{ $s }}" {{ request('semillero') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>

            <select name="tipo_programa" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-xl border text-sm outline-none"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los tipos</option>
                <option value="Tecnólogo" {{ request('tipo_programa') === 'Tecnólogo' ? 'selected' : '' }}>Tecnólogo</option>
                <option value="Técnico" {{ request('tipo_programa') === 'Técnico' ? 'selected' : '' }}>Técnico</option>
                <option value="Complementaria" {{ request('tipo_programa') === 'Complementaria' ? 'selected' : '' }}>Complementaria</option>
            </select>

            @if(request()->anyFilled(['search', 'semillero', 'tipo_programa']))
                <a href="{{ route('superadmin.proyectos.aprendices.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-500 hover:text-gray-800">
                    Limpiar
                </a>
            @endif
        </form>

        <div class="text-xs font-bold px-3 py-1.5 rounded-full" style="background:#fdf9ee; color:#71277a; border: 1px solid #e7e0cc;">
            Total: {{ $aprendices->total() }} aprendiz{{ $aprendices->total() != 1 ? 'es' : '' }}
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider font-extrabold" style="background:#fafafa; border-bottom:1px solid #f3f0e8; color:#5a5a4f;">
                        <th class="py-3.5 px-4">Aprendiz / Documento</th>
                        <th class="py-3.5 px-4">Contacto</th>
                        <th class="py-3.5 px-4">Programa & Ficha</th>
                        <th class="py-3.5 px-4">Semillero</th>
                        <th class="py-3.5 px-4">Proyecto Asignado</th>
                        <th class="py-3.5 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($aprendices as $a)
                    <tr class="hover:bg-amber-50/20 transition">
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-sm" style="color:#1c2b16;">{{ $a->nombre_completo }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                <span class="px-1.5 py-0.2 rounded font-bold text-[10px]" style="background:#f3f0e8; color:#71277a;">{{ $a->tipo_documento }}</span>
                                <span>{{ $a->numero_documento }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="font-bold text-[11px] text-red-600">{{ $a->rh }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs">
                            <div class="text-gray-700 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ $a->telefono }}
                            </div>
                            <div class="text-gray-500 flex items-center gap-1 mt-1 truncate max-w-[180px]" title="{{ $a->correo_electronico }}">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                {{ $a->correo_electronico }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-xs">
                            <div class="font-bold text-gray-800 line-clamp-1" title="{{ $a->programa_completo }}">{{ $a->programa_completo }}</div>
                            <div class="text-gray-500 mt-0.5 flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" style="background:#e8f5e9; color:#2e7d32;">{{ $a->tipo_programa }}</span>
                                <span>Ficha: <strong>{{ $a->ficha_programa }}</strong></span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background:#f3e8ff; color:#71277a;">
                                {{ $a->semillero }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-xs text-gray-600 max-w-xs">
                            <div class="line-clamp-2" title="{{ $a->nombre_proyecto }}">
                                {{ $a->nombre_proyecto }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-1 line-clamp-1" title="{{ $a->instructor_lider_asesores }}">
                                Inst: {{ $a->instructor_lider_asesores }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick='verDetalleAprendiz(@json($a))'
                                        class="p-1.5 rounded-lg border hover:bg-gray-100 transition"
                                        style="border-color:#e7e0cc; color:#1c2b16;" title="Ver ficha completa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                                @if(auth()->user()->role_id === 1)
                                <form method="POST" action="{{ route('superadmin.proyectos.aprendices.destroy', $a->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este registro de aprendiz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg border hover:bg-red-50 transition"
                                            style="border-color:#fca5a5; color:#ef4444;" title="Eliminar registro">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400 text-sm">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <p class="font-bold text-gray-600">No se encontraron aprendices registrados</p>
                            <p class="text-xs text-gray-400 mt-1">Registra nuevos aprendices desde el botón en el módulo de Proyectos.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($aprendices->hasPages())
        <div class="px-6 py-4 border-t" style="border-color:#f3f0e8;">
            {{ $aprendices->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL VER DETALLE COMPLETO --}}
<div id="modalDetalleAprendiz" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white" style="background:#39a900;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold" style="color:#1c2b16;" id="detNombre">Ficha del Aprendiz</h3>
                    <p class="text-xs text-gray-500" id="detDoc"></p>
                </div>
            </div>
            <button onclick="cerrarDetalleAprendiz()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="p-6 space-y-6 text-sm">
            {{-- DATOS PERSONALES --}}
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider pb-2 border-b mb-3" style="color:#71277a; border-color:#f3f0e8;">1. Datos Personales</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Tipo Documento</span>
                        <span class="font-bold text-gray-800 text-sm" id="detTipoDoc">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Número Documento</span>
                        <span class="font-bold text-gray-800 text-sm" id="detNumDoc">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Género</span>
                        <span class="font-bold text-gray-800 text-sm" id="detGenero">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">RH</span>
                        <span class="font-bold text-red-600 text-sm" id="detRh">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">EPS</span>
                        <span class="font-bold text-gray-800 text-sm" id="detEps">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Teléfono</span>
                        <span class="font-bold text-gray-800 text-sm" id="detTelefono">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50 col-span-2 sm:col-span-3">
                        <span class="block text-gray-400 font-semibold">Correo Electrónico</span>
                        <span class="font-bold text-gray-800 text-sm" id="detCorreo">—</span>
                    </div>
                </div>
            </div>

            {{-- DATOS ACADÉMICOS --}}
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider pb-2 border-b mb-3" style="color:#71277a; border-color:#f3f0e8;">2. Formación Académica</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Tipo de Programa</span>
                        <span class="font-bold text-gray-800 text-sm" id="detTipoProg">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold">Ficha de Formación</span>
                        <span class="font-bold text-gray-800 text-sm" id="detFicha">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50 col-span-2">
                        <span class="block text-gray-400 font-semibold">Programa de Formación</span>
                        <span class="font-bold text-gray-800 text-sm" id="detPrograma">—</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-gray-50 col-span-2">
                        <span class="block text-gray-400 font-semibold">Auxilio / Apoyo Económico SENA</span>
                        <span class="font-bold text-gray-800 text-sm" id="detApoyo">—</span>
                    </div>
                </div>
            </div>

            {{-- INVESTIGACIÓN --}}
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider pb-2 border-b mb-3" style="color:#71277a; border-color:#f3f0e8;">3. Proyecto & Semillero</h4>
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-purple-50/50 border border-purple-100">
                        <span class="block text-purple-700 font-bold mb-1">Semillero de Investigación</span>
                        <span class="text-sm font-black text-purple-900" id="detSemillero">—</span>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold mb-1">Nombre del Proyecto de Investigación</span>
                        <p class="text-sm font-semibold text-gray-800 leading-relaxed whitespace-pre-wrap" id="detProyectoDesc">—</p>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50">
                        <span class="block text-gray-400 font-semibold mb-1">Instructor Líder y Asesores</span>
                        <p class="text-sm font-semibold text-gray-800 leading-relaxed whitespace-pre-wrap" id="detInstructores">—</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4 border-t bg-gray-50 rounded-b-2xl flex justify-end">
            <button onclick="cerrarDetalleAprendiz()" class="px-5 py-2 rounded-xl text-xs font-bold bg-white border border-gray-300 hover:bg-gray-100 text-gray-700">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
function verDetalleAprendiz(a) {
    document.getElementById('detNombre').textContent = a.nombre_completo || 'Aprendiz';
    document.getElementById('detDoc').textContent = (a.tipo_documento || '') + ' ' + (a.numero_documento || '');
    document.getElementById('detTipoDoc').textContent = a.tipo_documento + (a.tipo_documento_otro ? ' (' + a.tipo_documento_otro + ')' : '');
    document.getElementById('detNumDoc').textContent = a.numero_documento || '—';
    document.getElementById('detGenero').textContent = a.genero || '—';
    document.getElementById('detRh').textContent = a.rh || '—';
    document.getElementById('detEps').textContent = a.eps || '—';
    document.getElementById('detTelefono').textContent = a.telefono || '—';
    document.getElementById('detCorreo').textContent = a.correo_electronico || '—';
    document.getElementById('detTipoProg').textContent = a.tipo_programa || '—';
    document.getElementById('detFicha').textContent = a.ficha_programa || '—';
    document.getElementById('detPrograma').textContent = (a.programa_formacion === 'OTRO' && a.programa_formacion_otro) ? 'OTRO: ' + a.programa_formacion_otro : (a.programa_formacion || '—');
    document.getElementById('detApoyo').textContent = (a.apoyo_sena === 'OTRO' && a.apoyo_sena_otro) ? 'OTRO: ' + a.apoyo_sena_otro : (a.apoyo_sena || 'NINGUNO');
    document.getElementById('detSemillero').textContent = a.semillero || '—';
    document.getElementById('detProyectoDesc').textContent = a.nombre_proyecto || '—';
    document.getElementById('detInstructores').textContent = a.instructor_lider_asesores || '—';

    document.getElementById('modalDetalleAprendiz').classList.remove('hidden');
    document.getElementById('modalDetalleAprendiz').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarDetalleAprendiz() {
    document.getElementById('modalDetalleAprendiz').classList.add('hidden');
    document.getElementById('modalDetalleAprendiz').classList.remove('flex');
    document.body.style.overflow = '';
}

document.getElementById('modalDetalleAprendiz').addEventListener('click', function(e) {
    if (e.target === this) cerrarDetalleAprendiz();
});
</script>

</x-dynamic-component>
