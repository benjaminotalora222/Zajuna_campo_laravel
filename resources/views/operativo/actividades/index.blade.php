@php $title = 'Ejecución de Actividades'; @endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Ejecución de Actividades</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Consulta el avance y estado de las actividades planificadas en tus proyectos.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nueva Actividad
        </button>
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('operativo.actividades.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar actividad o proyecto..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <select name="proyecto_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los proyectos</option>
                    @foreach($proyectos as $p)
                        <option value="{{ $p->id }}" {{ request('proyecto_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[160px]">
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los estados</option>
                    <option value="pendiente"    {{ request('estado') === 'pendiente'    ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_ejecucion" {{ request('estado') === 'en_ejecucion' ? 'selected' : '' }}>En Ejecución</option>
                    <option value="finalizado"   {{ request('estado') === 'finalizado'   ? 'selected' : '' }}>Finalizado</option>
                    <option value="pausado"      {{ request('estado') === 'pausado'      ? 'selected' : '' }}>Pausado</option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <input type="date" name="fecha" value="{{ request('fecha') }}"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','proyecto_id','estado','fecha']))
                <a href="{{ route('operativo.actividades.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:2px solid #f3f0e8;">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Actividad</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Proyecto / Espacio</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Progreso</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Responsable</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Fecha Límite</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $act)
                @php
                    $estadoConfig = match($act->estado) {
                        'en_ejecucion' => ['label' => 'En Ejecución', 'bg' => '#fffbeb', 'color' => '#d97706', 'dot' => '#fdc300'],
                        'finalizado'   => ['label' => 'Finalizado',   'bg' => '#f0fdf4', 'color' => '#166534', 'dot' => '#39a900'],
                        'pausado'      => ['label' => 'Pausado',      'bg' => '#f3f4f6', 'color' => '#6b7280', 'dot' => '#9ca3af'],
                        default        => ['label' => 'Pendiente',    'bg' => '#faf5ff', 'color' => '#71277a', 'dot' => '#71277a'],
                    };
                    $pct = (int) ($act->progreso ?? 0);
                    $barColor = $act->estado === 'finalizado' ? '#39a900' : '#fdc300';
                @endphp
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                    <td class="px-6 py-4" style="max-width:240px;">
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-sm leading-snug" style="color:#1c2b16;">{{ $act->tema }}</p>
                        </div>
                        @if($act->descripcion)
                            <p class="text-xs mt-0.5 line-clamp-2" style="color:#9a9a8a;">{{ $act->descripcion }}</p>
                        @endif
                        @php
                            $todosDocs = $act->documentos ?? [];
                            if (empty($todosDocs) && $act->documento) {
                                $todosDocs = [[
                                    'path' => $act->documento,
                                    'nombre' => $act->documento_nombre ?? 'Documento',
                                    'tamano' => ''
                                ]];
                            }
                        @endphp
                        @if(count($todosDocs) > 0)
                            <div class="mt-2 flex items-center">
                                @if(count($todosDocs) === 1)
                                    <a href="{{ asset('storage/' . $todosDocs[0]['path']) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition hover:opacity-90"
                                       style="background:#f5f3ff; color:#71277a; border:1px solid #ddd6fe;"
                                       title="{{ $todosDocs[0]['nombre'] }}">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                        <span class="truncate max-w-[130px]">{{ $todosDocs[0]['nombre'] }}</span>
                                        <svg class="w-3 h-3 opacity-60 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                            <polyline points="15 3 21 3 21 9"/>
                                            <line x1="10" y1="14" x2="21" y2="3"/>
                                        </svg>
                                    </a>
                                @else
                                    <details class="relative">
                                        <summary class="list-none inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold cursor-pointer transition hover:opacity-90 select-none"
                                                 style="background:#f5f3ff; color:#71277a; border:1px solid #ddd6fe;">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            <span>{{ count($todosDocs) }} documentos</span>
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="m6 9 6 6 6-6"/>
                                            </svg>
                                        </summary>
                                        <div class="absolute left-0 mt-1.5 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-2 z-30 space-y-1">
                                            @foreach($todosDocs as $d)
                                                <a href="{{ asset('storage/' . $d['path']) }}" target="_blank"
                                                   class="flex items-center justify-between p-2 rounded-lg text-xs hover:bg-purple-50 transition font-medium text-gray-700 hover:text-purple-900">
                                                    <span class="truncate pr-2">📄 {{ $d['nombre'] }}</span>
                                                    <span class="text-[10px] text-gray-400 shrink-0">{{ $d['tamano'] ?? '' }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($act->proyecto)
                            <span class="text-sm font-medium" style="color:#5a5a4f;">{{ $act->proyecto->nombre }}</span>
                        @elseif($act->evento_id || $act->evento)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                                  style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span>Cronograma: {{ $act->espacio_fisico ?: 'Espacio general' }}</span>
                            </span>
                        @else
                            <span class="text-sm" style="color:#9a9a8a;">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4" style="min-width:140px;">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 rounded-full overflow-hidden" style="background:#f3f0e8;">
                                <div class="h-full rounded-full transition-all"
                                     style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                            </div>
                            <span class="text-xs font-bold w-8 text-right" style="color:#1c2b16;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $estadoConfig['bg'] }}; color:{{ $estadoConfig['color'] }};">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $estadoConfig['dot'] }};"></span>
                            {{ $estadoConfig['label'] }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        @if($act->responsableUser)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white shrink-0"
                                 style="background:#71277a;">
                                {{ strtoupper(substr($act->responsableUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold" style="color:#1c2b16;">{{ $act->responsableUser->name }}</p>
                            </div>
                        </div>
                        @else
                            <span style="color:#9a9a8a;">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($act->fecha_limite)
                            <p class="text-xs font-semibold" style="color:#1c2b16;">{{ $act->fecha_limite->locale('es')->isoFormat('D MMM YYYY') }}</p>
                            <p class="text-xs" style="color:#9a9a8a;">{{ $act->fecha_limite->locale('es')->diffForHumans() }}</p>
                        @else
                            <span style="color:#9a9a8a;">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick='abrirModalEditar(@json($act))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>
                            <button onclick="confirmarEliminar({{ $act->id }})"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#fef2f2; color:#ef4444;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center" style="color:#9a9a8a;">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                        </svg>
                        <p class="text-sm font-semibold">No hay actividades registradas</p>
                        <p class="text-xs mt-1">Crea la primera con el botón "Nueva Actividad".</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($actividades->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">Mostrando {{ $actividades->firstItem() }} a {{ $actividades->lastItem() }} de {{ $actividades->total() }} actividades</p>
            <div class="flex items-center gap-1">
                @if($actividades->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></span>
                @else
                    <a href="{{ $actividades->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></a>
                @endif
                @foreach($actividades->getUrlRange(max(1,$actividades->currentPage()-2), min($actividades->lastPage(),$actividades->currentPage()+2)) as $page => $url)
                    @if($page == $actividades->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                    @endif
                @endforeach
                @if($actividades->hasMorePages())
                    <a href="{{ $actividades->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></span>
                @endif
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">
            Mostrando {{ $actividades->count() }} actividad{{ $actividades->count() != 1 ? 'es' : '' }}
        </div>
        @endif
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <h2 id="modalTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nueva Actividad</h2>
            <button onclick="cerrarModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formActividad" method="POST" enctype="multipart/form-data" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Tema / Nombre <span style="color:#ef4444;">*</span></label>
                <input type="text" name="tema" id="inpTema" required placeholder="Ej: Investigación de Suelos"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                <textarea name="descripcion" id="inpDescripcion" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Proyecto</label>
                    <select name="proyecto_id" id="inpProyecto" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Sin proyecto</option>
                        @foreach($proyectos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Responsable</label>
                    <select name="responsable_id" id="inpResponsable" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Sin asignar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Estado <span style="color:#ef4444;">*</span></label>
                    <select name="estado" id="inpEstado" required class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="pendiente">Pendiente</option>
                        <option value="en_ejecucion">En Ejecución</option>
                        <option value="finalizado">Finalizado</option>
                        <option value="pausado">Pausado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Progreso (%)</label>
                    <input type="number" name="progreso" id="inpProgreso" min="0" max="100" value="0"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha límite</label>
                    <input type="date" name="fecha_limite" id="inpFecha"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>

                {{-- Campo para anexar documentos múltiples --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                        Anexar Documentos o Soportes
                        <span class="text-xs font-normal" style="color:#9a9a8a;">(Puedes seleccionar varios archivos: PDF, Word, Excel, imagen o ZIP - máx 20MB c/u)</span>
                    </label>

                    {{-- Lista de documentos actuales en modo edición --}}
                    <div id="docActualesWrap" class="hidden mb-3">
                        <p class="text-xs font-bold mb-1.5" style="color:#5a5a4f;">Documentos guardados actualmente:</p>
                        <div id="docActualesList" class="space-y-1.5 max-h-36 overflow-y-auto pr-1"></div>
                        <div id="inputsEliminarDocs"></div>
                    </div>

                    {{-- Selector de archivos múltiples --}}
                    <label class="flex items-center gap-3 px-4 py-3 rounded-xl border border-dashed cursor-pointer hover:border-[#39a900] transition group"
                           style="border-color:#d4cfc0; background:#fafafa;">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition"
                             style="background:#f0fdf4; color:#39a900;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p id="docNombreSeleccionado" class="text-xs font-semibold truncate" style="color:#1c2b16;">Haz clic para seleccionar uno o varios documentos</p>
                            <p class="text-[11px]" style="color:#9a9a8a;">Mantén presionado Ctrl o Shift para elegir múltiples archivos</p>
                        </div>
                        <input type="file" name="documentos[]" id="inpDocumentos" multiple class="hidden" onchange="mostrarNombresArchivos(this)">
                    </label>

                    {{-- Lista de nuevos archivos seleccionados para subir --}}
                    <div id="nuevosDocsList" class="hidden mt-2 p-2.5 rounded-xl border space-y-1 text-xs" style="background:#f0fdf4; border-color:#bbf7d0;">
                        <div class="flex items-center justify-between font-bold text-emerald-800 text-[11px] mb-1">
                            <span id="nuevosDocsConteo">Archivos listos para subir:</span>
                            <button type="button" onclick="limpiarNuevosDocs()" class="text-emerald-700 hover:underline text-[10px]">Limpiar selección</button>
                        </div>
                        <ul id="nuevosDocsItems" class="space-y-1 max-h-28 overflow-y-auto pr-1 font-medium text-emerald-900"></ul>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#39a900;">Crear actividad</button>
                <button type="button" onclick="cerrarModal()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL CONFIRMAR ELIMINAR ══════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-7 text-center" onclick="event.stopPropagation()">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
            <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            </svg>
        </div>
        <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar actividad?</h3>
        <p class="text-sm mb-6" style="color:#5a5a4f;">Esta acción no se puede deshacer.</p>
        <form id="formEliminar" method="POST">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white" style="background:#ef4444;">Sí, eliminar</button>
                <button type="button" onclick="cerrarEliminar()" class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal() {
    document.getElementById('modalForm').classList.add('hidden');
    document.getElementById('modalForm').classList.remove('flex');
    document.body.style.overflow = '';
}
function cerrarEliminar() {
    document.getElementById('modalEliminar').classList.add('hidden');
    document.getElementById('modalEliminar').classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('modalForm').addEventListener('click', function(e) { if (e.target === this) cerrarModal(); });
document.getElementById('modalEliminar').addEventListener('click', function(e) { if (e.target === this) cerrarEliminar(); });

function mostrarNombresArchivos(input) {
    const listWrap = document.getElementById('nuevosDocsList');
    const itemsUl = document.getElementById('nuevosDocsItems');
    const conteo = document.getElementById('nuevosDocsConteo');
    const lbl = document.getElementById('docNombreSeleccionado');

    itemsUl.innerHTML = '';

    if (input.files && input.files.length > 0) {
        lbl.textContent = input.files.length + ' archivo(s) seleccionado(s)';
        lbl.style.color = '#166534';
        conteo.textContent = input.files.length + ' archivo(s) listo(s) para subir:';
        listWrap.classList.remove('hidden');

        for (let i = 0; i < input.files.length; i++) {
            const f = input.files[i];
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-2 py-0.5 border-b border-emerald-100 last:border-0';
            li.innerHTML = '<span class="truncate">📄 ' + f.name + '</span><span class="text-[10px] text-emerald-700 shrink-0 font-bold">' + (f.size / 1024).toFixed(1) + ' KB</span>';
            itemsUl.appendChild(li);
        }
    } else {
        limpiarNuevosDocs();
    }
}

function limpiarNuevosDocs() {
    const inp = document.getElementById('inpDocumentos');
    if (inp) inp.value = '';
    const lbl = document.getElementById('docNombreSeleccionado');
    if (lbl) {
        lbl.textContent = 'Haz clic para seleccionar uno o varios documentos';
        lbl.style.color = '#1c2b16';
    }
    const listWrap = document.getElementById('nuevosDocsList');
    if (listWrap) listWrap.classList.add('hidden');
    const itemsUl = document.getElementById('nuevosDocsItems');
    if (itemsUl) itemsUl.innerHTML = '';
}

function renderDocsGuardados(docs) {
    const wrap = document.getElementById('docActualesWrap');
    const list = document.getElementById('docActualesList');
    const inputsEliminar = document.getElementById('inputsEliminarDocs');

    wrap.classList.add('hidden');
    list.innerHTML = '';
    inputsEliminar.innerHTML = '';

    if (!docs || docs.length === 0) return;

    wrap.classList.remove('hidden');

    docs.forEach((doc, idx) => {
        const item = document.createElement('div');
        item.id = 'docGuardadoItem_' + idx;
        item.className = 'p-2.5 rounded-xl border flex items-center justify-between gap-3 text-xs transition';
        item.style.background = '#f8fafc';
        item.style.borderColor = '#e2e8f0';

        item.innerHTML = `
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:#f3e8ff; color:#71277a;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="font-bold truncate text-gray-800">${doc.nombre}</p>
                    <a href="/storage/${doc.path}" target="_blank" class="text-[11px] font-semibold hover:underline" style="color:#71277a;">
                        Ver / Descargar ${doc.tamano ? '(' + doc.tamano + ')' : ''}
                    </a>
                </div>
            </div>
            <button type="button" onclick="marcarEliminarDoc('${doc.path}', ${idx})"
                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-red-600 hover:bg-red-50 transition shrink-0 border border-red-200">
                Eliminar
            </button>
        `;
        list.appendChild(item);
    });
}

function marcarEliminarDoc(path, idx) {
    const item = document.getElementById('docGuardadoItem_' + idx);
    const inputsEliminar = document.getElementById('inputsEliminarDocs');
    if (!item) return;

    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'eliminar_docs[]';
    hidden.value = path;
    inputsEliminar.appendChild(hidden);

    item.style.opacity = '0.45';
    item.style.background = '#fef2f2';
    item.style.borderColor = '#fca5a5';
    const btn = item.querySelector('button');
    if (btn) btn.remove();
    const span = document.createElement('span');
    span.className = 'text-[10px] text-red-600 font-bold shrink-0';
    span.textContent = 'Marcado para eliminar';
    item.appendChild(span);
}

function abrirModalCrear() {
    document.getElementById('modalTitle').textContent  = 'Nueva Actividad';
    document.getElementById('btnGuardar').textContent  = 'Crear actividad';
    document.getElementById('formActividad').action    = '{{ route("operativo.actividades.store") }}';
    document.getElementById('formMethod').value        = 'POST';
    document.getElementById('inpTema').value           = '';
    document.getElementById('inpDescripcion').value    = '';
    document.getElementById('inpProyecto').value       = '';
    document.getElementById('inpResponsable').value    = '';
    document.getElementById('inpEstado').value         = 'pendiente';
    document.getElementById('inpProgreso').value       = '0';
    document.getElementById('inpFecha').value          = '';
    limpiarNuevosDocs();
    document.getElementById('docActualesWrap').classList.add('hidden');
    document.getElementById('docActualesList').innerHTML = '';
    document.getElementById('inputsEliminarDocs').innerHTML = '';
    abrirModal('modalForm');
}

function abrirModalEditar(act) {
    document.getElementById('modalTitle').textContent  = 'Editar Actividad';
    document.getElementById('btnGuardar').textContent  = 'Guardar cambios';
    document.getElementById('formActividad').action    = '/operativo/actividades/' + act.id;
    document.getElementById('formMethod').value        = 'PUT';
    document.getElementById('inpTema').value           = act.tema || '';
    document.getElementById('inpDescripcion').value    = act.descripcion || '';
    document.getElementById('inpProyecto').value       = act.proyecto_id || '';
    document.getElementById('inpResponsable').value    = act.responsable_id || '';
    document.getElementById('inpEstado').value         = act.estado || 'pendiente';
    document.getElementById('inpProgreso').value       = act.progreso || 0;
    document.getElementById('inpFecha').value          = act.fecha_limite ? act.fecha_limite.substring(0,10) : '';
    limpiarNuevosDocs();

    let docs = act.documentos || [];
    if ((!docs || docs.length === 0) && act.documento) {
        docs = [{
            path: act.documento,
            nombre: act.documento_nombre || 'Documento adjunto',
            tamano: ''
        }];
    }
    renderDocsGuardados(docs);

    abrirModal('modalForm');
}

function confirmarEliminar(id) {
    document.getElementById('formEliminar').action = '/operativo/actividades/' + id;
    abrirModal('modalEliminar');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-operativo-layout>
