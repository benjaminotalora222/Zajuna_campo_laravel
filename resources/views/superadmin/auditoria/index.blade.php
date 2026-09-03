@php $title = 'Registro de Auditoría'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold leading-tight">Registro de Auditoría</h1>
        <p class="text-sm mt-1" style="color:#5a5a4f;">Consulta y rastrea todas las acciones realizadas en el sistema.</p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Registros hoy</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $kpiHoy }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-5 h-5" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/><circle cx="8.5" cy="7" r="3"/>
                    <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Usuarios activos hoy</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $kpiUsuarios }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#fef2f2;">
                <svg class="w-5 h-5" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Errores hoy</p>
                <p class="text-2xl font-extrabold" style="color:#ef4444;">{{ $kpiFallidos }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Total registros</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ number_format($kpiTotal) }}</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-5">
        <form method="GET" action="{{ route('superadmin.auditoria.index') }}"
              class="flex flex-wrap gap-3 items-end">

            {{-- Búsqueda --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Descripción, módulo, acción..."
                           class="w-full pl-9 pr-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>

            {{-- Usuario --}}
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Usuario</label>
                <select name="usuario_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Módulo --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Módulo</label>
                <select name="modulo" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $m)
                    <option value="{{ $m }}" {{ request('modulo') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Acción --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Acción</label>
                <select name="accion" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todas las acciones</option>
                    @foreach($acciones as $a)
                    <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Estado --}}
            <div class="min-w-[130px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Estado</label>
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    <option value="exitoso" {{ request('estado') === 'exitoso' ? 'selected' : '' }}>Exitoso</option>
                    <option value="fallido" {{ request('estado') === 'fallido' ? 'selected' : '' }}>Fallido</option>
                </select>
            </div>

            {{-- Fecha desde --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            {{-- Fecha hasta --}}
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            {{-- Botones --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90 transition"
                        style="background:#39a900;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    Filtrar
                </button>
                @if(request()->anyFilled(['search','usuario_id','modulo','accion','estado','desde','hasta']))
                <a href="{{ route('superadmin.auditoria.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50 transition"
                   style="border-color:#e7e0cc; color:#5a5a4f;">
                    Limpiar
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <p class="text-sm font-semibold" style="color:#5a5a4f;">
                {{ $logs->total() }} registro{{ $logs->total() !== 1 ? 's' : '' }} encontrado{{ $logs->total() !== 1 ? 's' : '' }}
            </p>
            <span class="text-xs px-3 py-1 rounded-full font-bold" style="background:#f0fdf4; color:#39a900;">
                Página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="background:#fafafa; border-bottom:1.5px solid #f0f0f0;">
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Fecha y Hora</th>
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Usuario</th>
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acción</th>
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Módulo</th>
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Detalle</th>
                        <th class="text-left px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    {{-- Fecha --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
                                 style="background:{{ $log->estado === 'fallido' ? '#fef2f2' : '#f0fdf4' }};">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                     style="color:{{ $log->estado === 'fallido' ? '#ef4444' : '#39a900' }};"
                                     viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold" style="color:#1c2b16;">
                                    {{ $log->fechaHora ? $log->fechaHora->format('d/m/Y') : ($log->created_at ? $log->created_at->format('d/m/Y') : '—') }}
                                </p>
                                <p class="text-xs" style="color:#9a9a8a;">
                                    {{ $log->fechaHora ? $log->fechaHora->format('H:i:s') : ($log->created_at ? $log->created_at->format('H:i:s') : '') }}
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Usuario --}}
                    <td class="px-5 py-3.5">
                        @if($log->usuario)
                        @php
                            $parts = explode(' ', $log->usuario->name);
                            $initials = strtoupper(substr($parts[0],0,1)) . strtoupper(substr($parts[1] ?? '',0,1));
                            $colors = ['#39a900','#71277a','#fdc300','#3b82f6','#ef4444','#f97316'];
                            $color  = $colors[$log->idUsuario % count($colors)];
                        @endphp
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-extrabold shrink-0"
                                 style="background:{{ $color }};">
                                {{ $initials }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold" style="color:#1c2b16;">{{ $log->usuario->name }}</p>
                                <p class="text-xs" style="color:#9a9a8a;">
                                    {{ $log->usuario->rol->nombre ?? ($log->usuario->role_id === 1 ? 'Superadmin' : 'Operativo') }}
                                </p>
                            </div>
                        </div>
                        @else
                        <span class="text-xs" style="color:#9a9a8a;">Sistema</span>
                        @endif
                    </td>

                    {{-- Acción --}}
                    <td class="px-5 py-3.5">
                        @php
                            $accionColor = match(strtolower($log->accion ?? '')) {
                                'creación', 'creacion', 'crear'       => ['bg'=>'#f0fdf4','cl'=>'#166534'],
                                'actualización', 'actualizacion', 'editar', 'actualizar' => ['bg'=>'#fffbeb','cl'=>'#d97706'],
                                'eliminación', 'eliminacion', 'eliminar' => ['bg'=>'#fef2f2','cl'=>'#ef4444'],
                                'inicio de sesión', 'login'           => ['bg'=>'#f0f9ff','cl'=>'#0369a1'],
                                'cierre de sesión', 'logout'          => ['bg'=>'#f3f4f6','cl'=>'#6b7280'],
                                default                               => ['bg'=>'#f3e8ff','cl'=>'#71277a'],
                            };
                        @endphp
                        @if($log->accion)
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold"
                              style="background:{{ $accionColor['bg'] }}; color:{{ $accionColor['cl'] }};">
                            {{ $log->accion }}
                        </span>
                        @else
                        <span class="text-xs" style="color:#9a9a8a;">—</span>
                        @endif
                    </td>

                    {{-- Módulo --}}
                    <td class="px-5 py-3.5">
                        @if($log->modulo)
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold"
                              style="background:#faf5ff; color:#71277a;">
                            {{ $log->modulo }}
                        </span>
                        @else
                        <span class="text-xs" style="color:#9a9a8a;">—</span>
                        @endif
                    </td>

                    {{-- Detalle --}}
                    <td class="px-5 py-3.5 max-w-xs">
                        <p class="text-xs" style="color:#1c2b16; line-height:1.5;">
                            {{ $log->descripcionOperacion ?: '—' }}
                        </p>
                    </td>

                    {{-- Estado --}}
                    <td class="px-5 py-3.5">
                        @if(($log->estado ?? 'exitoso') === 'fallido')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold"
                              style="background:#fef2f2; color:#ef4444;">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span>
                            Fallido
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold"
                              style="background:#f0fdf4; color:#166534;">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                            Exitoso
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12" style="color:#d1d5db;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
                            </svg>
                            <p class="text-sm font-semibold" style="color:#9a9a8a;">Sin registros de auditoría</p>
                            <p class="text-xs" style="color:#d1d5db;">Los registros aparecerán aquí cuando se realicen acciones en el sistema.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs" style="color:#9a9a8a;">
                Mostrando {{ $logs->firstItem() }}–{{ $logs->lastItem() }} de {{ $logs->total() }} registros
            </p>
            <div class="flex items-center gap-1">
                @if($logs->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border cursor-not-allowed"
                      style="border-color:#e7e0cc; color:#d1d5db;">← Anterior</span>
                @else
                <a href="{{ $logs->previousPageUrl() }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border hover:bg-gray-50 transition"
                   style="border-color:#e7e0cc; color:#5a5a4f;">← Anterior</a>
                @endif

                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition
                          {{ $page === $logs->currentPage() ? 'text-white' : 'border hover:bg-gray-50' }}"
                   style="{{ $page === $logs->currentPage() ? 'background:#39a900;' : 'border-color:#e7e0cc; color:#5a5a4f;' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold border hover:bg-gray-50 transition"
                   style="border-color:#e7e0cc; color:#5a5a4f;">Siguiente →</a>
                @else
                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border cursor-not-allowed"
                      style="border-color:#e7e0cc; color:#d1d5db;">Siguiente →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Nota sobre registros automáticos --}}
    <p class="mt-4 text-xs text-center" style="color:#d1d5db;">
        Los registros se generan automáticamente cuando los usuarios realizan acciones en el sistema.
    </p>

</div>
</x-superadmin-layout>
