@php
    $title = 'Cronograma de Espacios';

    $tipoConfig = [
        'visita_tecnica' => ['label' => 'Visita Técnica',  'color' => '#71277a', 'bg' => '#faf5ff', 'dot' => '#71277a'],
        'capacitacion'   => ['label' => 'Capacitación',    'color' => '#854d0e', 'bg' => '#fffbeb', 'dot' => '#fdc300'],
        'reunion'        => ['label' => 'Reunión',         'color' => '#1e40af', 'bg' => '#dbeafe', 'dot' => '#3b82f6'],
        'otro'           => ['label' => 'Otro',            'color' => '#374151', 'bg' => '#f3f4f6', 'dot' => '#9ca3af'],
    ];

    // Calcular días del calendario (incluye días del mes anterior/siguiente para completar semanas)
    $primerDia   = $inicio->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
    $ultimoDia   = $fin->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
    $diasCalendario = collect();
    $cursor = $primerDia->copy();
    while ($cursor->lte($ultimoDia)) {
        $diasCalendario->push($cursor->copy());
        $cursor->addDay();
    }

    $mesAnterior = $inicio->copy()->subMonth();
    $mesSiguiente = $inicio->copy()->addMonth();

    $nombresMes = [
        1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
        7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
    ];
@endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Calendario</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Organiza y visualiza tus actividades y eventos.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Hoy --}}
            <a href="{{ route('operativo.cronograma.index') }}"
               class="px-4 py-2 rounded-xl text-sm font-bold border hover:bg-gray-50 transition"
               style="border-color:#e7e0cc; color:#5a5a4f;">Hoy</a>

            {{-- Nuevo evento --}}
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm transition hover:opacity-90"
                    style="background:#71277a; color:white;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                Nuevo evento
            </button>

            {{-- Filtro tipo --}}
            <form method="GET" action="{{ route('operativo.cronograma.index') }}" class="flex gap-2">
                <input type="hidden" name="anio" value="{{ $anio }}">
                <input type="hidden" name="mes"  value="{{ $mes }}">
                <select name="tipo" onchange="this.form.submit()"
                        class="px-3 py-2 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los eventos</option>
                    @foreach($tipoConfig as $key => $tc)
                        <option value="{{ $key }}" {{ $filtroTipo === $key ? 'selected' : '' }}>{{ $tc['label'] }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- NAVEGACIÓN MES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-2">
        <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:#f3f0e8;">
            <a href="{{ route('operativo.cronograma.index', ['anio' => $mesAnterior->year, 'mes' => $mesAnterior->month, 'tipo' => $filtroTipo]) }}"
               class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50 border transition"
               style="border-color:#e7e0cc; color:#5a5a4f;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <h2 class="text-base font-extrabold" style="color:#1c2b16;">
                {{ $nombresMes[$mes] }} {{ $anio }}
            </h2>
            <a href="{{ route('operativo.cronograma.index', ['anio' => $mesSiguiente->year, 'mes' => $mesSiguiente->month, 'tipo' => $filtroTipo]) }}"
               class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50 border transition"
               style="border-color:#e7e0cc; color:#5a5a4f;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            </a>
        </div>

        {{-- Cabecera días --}}
        <div class="grid grid-cols-7 border-b" style="border-color:#f3f0e8;">
            @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $dia)
            <div class="py-3 text-center text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">{{ $dia }}</div>
            @endforeach
        </div>

        {{-- Celdas del calendario --}}
        <div class="grid grid-cols-7">
            @foreach($diasCalendario as $idx => $dia)
            @php
                $esMesActual = $dia->month === $mes;
                $esHoy       = $dia->isToday();
                $eventos_dia = $eventosPorDia->get($dia->day, collect())->filter(fn($e) => $e->fecha->month === $mes && $e->fecha->year === $anio);
                $esFin       = ($idx + 1) % 7 === 0;
                $bordeDer    = !$esFin ? 'border-r' : '';
                $bordeBajo   = $idx < count($diasCalendario) - 7 ? 'border-b' : '';
            @endphp
            <div class="min-h-[110px] p-2 {{ $bordeDer }} {{ $bordeBajo }} relative"
                 style="border-color:#f3f0e8; background:{{ $esHoy ? '#f0fdf4' : ($esMesActual ? 'white' : '#fafafa') }};"
                 onclick="abrirModalCrearFecha('{{ $dia->format('Y-m-d') }}')">

                {{-- Número del día --}}
                <div class="flex items-center justify-center w-7 h-7 rounded-full mb-1 text-sm font-bold
                            {{ $esHoy ? 'text-white' : ($esMesActual ? '' : 'opacity-30') }}"
                     style="{{ $esHoy ? 'background:#39a900;' : 'color:#1c2b16;' }}">
                    {{ $dia->day }}
                </div>

                {{-- Eventos del día --}}
                @if($esMesActual)
                    @foreach($eventos_dia->take(3) as $ev)
                    @php $tc = $tipoConfig[$ev->tipo] ?? $tipoConfig['otro']; @endphp
                    <div class="mb-0.5 px-1.5 py-0.5 rounded-md text-[11px] font-semibold leading-tight cursor-pointer hover:opacity-80 transition"
                         style="background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border-left: 3px solid {{ $tc['dot'] }};"
                         onclick="event.stopPropagation(); cargarEvento({{ $ev->id }})"
                         title="{{ $ev->titulo }}">
                        @if($ev->hora_inicio)
                            <span class="opacity-70">{{ \Carbon\Carbon::parse($ev->hora_inicio)->format('g:i A') }}</span>
                        @endif
                        <span class="block truncate">{{ $ev->titulo }}</span>
                    </div>
                    @endforeach
                    @if($eventos_dia->count() > 3)
                        <p class="text-[10px] font-bold mt-0.5 cursor-pointer" style="color:#71277a;"
                           onclick="event.stopPropagation();">
                            +{{ $eventos_dia->count() - 3 }} más
                        </p>
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- LEYENDA --}}
    <div class="flex flex-wrap items-center gap-5 px-2 py-3">
        @foreach($tipoConfig as $key => $tc)
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full" style="background:{{ $tc['dot'] }};"></span>
            <span class="text-xs font-semibold" style="color:#5a5a4f;">{{ $tc['label'] }}</span>
            <span class="text-xs" style="color:#9a9a8a;">
                — {{ $key === 'visita_tecnica' ? 'Actividades de campo y seguimiento' : ($key === 'capacitacion' ? 'Formación y talleres' : ($key === 'reunion' ? 'Reuniones de equipo' : 'Otros eventos')) }}
            </span>
        </div>
        @endforeach
        <span class="ml-auto text-xs" style="color:#9a9a8a;">Haz clic en un día para crear un evento · Haz clic en un evento para ver detalles</span>
    </div>

</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                    </svg>
                </div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Evento</h2>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-7 py-6 space-y-4">
            <div id="modalAlerta" class="hidden px-4 py-3 rounded-xl text-sm font-semibold" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;"></div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Título <span style="color:#ef4444;">*</span></label>
                <input type="text" id="inpTitulo" placeholder="Ej: Visita técnica Finca La Esperanza"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Tipo <span style="color:#ef4444;">*</span></label>
                    <select id="inpTipo" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="visita_tecnica">Visita Técnica</option>
                        <option value="capacitacion">Capacitación</option>
                        <option value="reunion">Reunión</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="inpFecha" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Hora inicio</label>
                    <input type="time" id="inpHoraInicio" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Hora fin</label>
                    <input type="time" id="inpHoraFin" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Espacio / Ubicación</label>
                <input type="text" id="inpEspacio" placeholder="Ej: Finca La Esperanza, Sala de reuniones..."
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                <textarea id="inpDescripcion" rows="2" placeholder="Detalles adicionales..."
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button id="btnEliminarEvento" onclick="eliminarEvento()" type="button"
                        class="hidden px-4 py-3 rounded-xl font-bold text-sm border hover:bg-red-50 transition"
                        style="border-color:#fca5a5; color:#ef4444;">Eliminar</button>
                <button id="btnGuardar" onclick="guardarEvento()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#71277a;">Crear evento</button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
let eventoActualId = null;

const ROUTES = {
    store:   '{{ route("superadmin.cronograma.store") }}',
    show:    '/operativo/cronograma/',
    update:  '/operativo/cronograma/',
    destroy: '/operativo/cronograma/',
    csrfToken: '{{ csrf_token() }}',
};

function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
    eventoActualId = null;
}
document.getElementById('modalForm').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalForm');
});

function limpiarForm() {
    document.getElementById('inpTitulo').value      = '';
    document.getElementById('inpTipo').value        = 'visita_tecnica';
    document.getElementById('inpFecha').value       = '';
    document.getElementById('inpHoraInicio').value  = '';
    document.getElementById('inpHoraFin').value     = '';
    document.getElementById('inpEspacio').value     = '';
    document.getElementById('inpDescripcion').value = '';
    document.getElementById('modalAlerta').classList.add('hidden');
    document.getElementById('btnEliminarEvento').classList.add('hidden');
}

function abrirModalCrear() {
    eventoActualId = null;
    limpiarForm();
    document.getElementById('modalFormTitle').textContent = 'Nuevo Evento';
    document.getElementById('btnGuardar').textContent = 'Crear evento';
    abrirModal('modalForm');
}

function abrirModalCrearFecha(fecha) {
    abrirModalCrear();
    document.getElementById('inpFecha').value = fecha;
}

async function cargarEvento(id) {
    const res  = await fetch(ROUTES.show + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const ev   = await res.json();
    eventoActualId = ev.id;
    limpiarForm();
    document.getElementById('modalFormTitle').textContent     = 'Editar Evento';
    document.getElementById('btnGuardar').textContent         = 'Guardar cambios';
    document.getElementById('inpTitulo').value                = ev.titulo;
    document.getElementById('inpTipo').value                  = ev.tipo;
    document.getElementById('inpFecha').value                 = ev.fecha;
    document.getElementById('inpHoraInicio').value            = ev.hora_inicio || '';
    document.getElementById('inpHoraFin').value               = ev.hora_fin    || '';
    document.getElementById('inpEspacio').value               = ev.espacio     || '';
    document.getElementById('inpDescripcion').value           = ev.descripcion || '';
    document.getElementById('btnEliminarEvento').classList.remove('hidden');
    abrirModal('modalForm');
}

async function guardarEvento() {
    const body = {
        titulo:      document.getElementById('inpTitulo').value.trim(),
        tipo:        document.getElementById('inpTipo').value,
        fecha:       document.getElementById('inpFecha').value,
        hora_inicio: document.getElementById('inpHoraInicio').value || null,
        hora_fin:    document.getElementById('inpHoraFin').value    || null,
        espacio:     document.getElementById('inpEspacio').value    || null,
        descripcion: document.getElementById('inpDescripcion').value || null,
        _token:      ROUTES.csrfToken,
    };

    if (!body.titulo || !body.fecha) {
        const al = document.getElementById('modalAlerta');
        al.textContent = 'El título y la fecha son obligatorios.';
        al.classList.remove('hidden');
        return;
    }

    let url    = ROUTES.store;
    let method = 'POST';
    if (eventoActualId) {
        url    = ROUTES.update + eventoActualId;
        method = 'PUT';
    }

    const res  = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': ROUTES.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(body),
    });
    const data = await res.json();

    if (data.ok) {
        cerrarModal('modalForm');
        location.reload();
    } else {
        const al = document.getElementById('modalAlerta');
        al.textContent = data.message || 'Error al guardar.';
        al.classList.remove('hidden');
    }
}

async function eliminarEvento() {
    if (!eventoActualId) return;
    if (!confirm('¿Eliminar este evento?')) return;

    const res  = await fetch(ROUTES.destroy + eventoActualId, {
        method:  'DELETE',
        headers: { 'X-CSRF-TOKEN': ROUTES.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
    });
    const data = await res.json();

    if (data.ok) {
        cerrarModal('modalForm');
        location.reload();
    }
}
</script>

</x-operativo-layout>
