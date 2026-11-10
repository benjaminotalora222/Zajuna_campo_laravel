@php
$title = 'Dashboard de Ventas por Proveedor';
$catColors = ['#fdc300','#71277a','#39a900','#ef4444','#3b82f6','#f97316','#8b5cf6','#06b6d4'];
@endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- SUB-NAVEGACIÓN / TABS DE VENTAS --}}
    <div class="flex items-center gap-2 mb-6 border-b pb-4" style="border-color:#f3f0e8;">
        <a href="{{ route('superadmin.ventas.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition hover:bg-gray-50"
           style="border-color:#e7e0cc; color:#5a5a4f; background:#fff;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span>Registro y Transacciones</span>
        </a>
        <a href="{{ route('superadmin.ventas.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition"
           style="background:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span>Dashboard por Proveedor</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#fdc300] text-[#71277a]">BI</span>
        </a>
        <a href="{{ route('superadmin.reportes.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border transition hover:bg-gray-50 ml-auto"
           style="border-color:#e7e0cc; color:#5a5a4f; background:#fff;">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Reportes Generales</span>
        </a>
    </div>

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Dashboard de Ventas por Proveedor</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Monitorea ingresos, volumen de productos, participación y evolución en rangos de fechas personalizados.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Botón Registrar Venta --}}
            <a href="{{ route('superadmin.ventas.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs transition hover:opacity-90 shadow-sm"
               style="background:#fdc300; color:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                <span>Nueva Venta</span>
            </a>
        </div>
    </div>

    {{-- FILTROS PRINCIPALES (PROVEEDOR + RANGO DE FECHAS) --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('superadmin.ventas.dashboard') }}" id="formFiltroDashboard"
              class="flex flex-wrap gap-4 items-end">

            {{-- Selector Proveedor --}}
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#71277a;">
                    Proveedor
                </label>
                <div class="relative">
                    <select name="proveedor_id" id="selectProveedor"
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-[#71277a]"
                            style="border-color:#e7e0cc; background:#fafafa; color:#1c2b16;">
                        <option value="">Todos los proveedores (Consolidado)</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}" {{ (string)$proveedorId === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->nombre }} ({{ $p->codigo ?? 'PRV-'.$p->id }})
                            </option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>

            {{-- Selector Período / Modo --}}
            <div class="min-w-[180px]">
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#71277a;">
                    Período / Rango
                </label>
                <select name="periodo" id="selectPeriodo" onchange="toggleFechasDashboard(this.value)"
                        class="w-full px-3 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-[#71277a]"
                        style="border-color:#e7e0cc; background:#fafafa; color:#1c2b16;">
                    <option value="este_mes"     {{ $periodo === 'este_mes' ? 'selected' : '' }}>Este mes</option>
                    <option value="hoy"          {{ $periodo === 'hoy' ? 'selected' : '' }}>Hoy</option>
                    <option value="ayer"         {{ $periodo === 'ayer' ? 'selected' : '' }}>Ayer</option>
                    <option value="7dias"        {{ $periodo === '7dias' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="15dias"       {{ $periodo === '15dias' ? 'selected' : '' }}>Últimos 15 días</option>
                    <option value="30dias"       {{ $periodo === '30dias' ? 'selected' : '' }}>Últimos 30 días</option>
                    <option value="mes_anterior" {{ $periodo === 'mes_anterior' ? 'selected' : '' }}>Mes anterior</option>
                    <option value="anio_actual"  {{ $periodo === 'anio_actual' ? 'selected' : '' }}>Año actual ({{ now()->year }})</option>
                    <option value="rango"        {{ $periodo === 'rango' ? 'selected' : '' }}>Personalizado (Calendario)</option>
                </select>
            </div>

            {{-- Fecha Inicio --}}
            <div id="divFechaInicio" class="min-w-[150px] {{ in_array($periodo, ['rango']) ? '' : 'hidden' }}">
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#5a5a4f;">Desde</label>
                <input type="date" name="fecha_inicio" id="inputFechaInicio"
                       value="{{ $fechaInicio ?? $inicio->toDateString() }}"
                       onclick="this.showPicker && this.showPicker()"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none cursor-pointer transition focus:border-[#71277a]"
                       style="border-color:#e7e0cc; background:#fafafa; color:#1c2b16;">
            </div>

            {{-- Fecha Fin --}}
            <div id="divFechaFin" class="min-w-[150px] {{ in_array($periodo, ['rango']) ? '' : 'hidden' }}">
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#5a5a4f;">Hasta</label>
                <input type="date" name="fecha_fin" id="inputFechaFin"
                       value="{{ $fechaFin ?? $fin->toDateString() }}"
                       onclick="this.showPicker && this.showPicker()"
                       class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none cursor-pointer transition focus:border-[#71277a]"
                       style="border-color:#e7e0cc; background:#fafafa; color:#1c2b16;">
            </div>

            {{-- Botones de Filtrar y Limpiar --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90 transition shadow-sm"
                        style="background:#39a900;">
                    Filtrar Dashboard
                </button>
                @if(request()->hasAny(['proveedor_id', 'fecha_inicio', 'fecha_fin']) || (request('periodo') && request('periodo') !== 'este_mes'))
                    <a href="{{ route('superadmin.ventas.dashboard') }}"
                       class="px-4 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50 transition"
                       style="border-color:#e7e0cc; color:#5a5a4f;">
                        Restablecer
                    </a>
                @endif
            </div>

            {{-- Atajos rápidos de presets --}}
            <div class="w-full flex items-center gap-1.5 flex-wrap pt-3 mt-1 border-t text-xs" style="border-color:#f1eee4;">
                <span class="font-bold mr-1 flex items-center gap-1 text-gray-500">
                    <svg class="w-3.5 h-3.5 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Atajos rápidos:
                </span>
                <button type="button" onclick="aplicarPreset('hoy')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === 'hoy' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Hoy</button>
                <button type="button" onclick="aplicarPreset('ayer')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === 'ayer' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Ayer</button>
                <button type="button" onclick="aplicarPreset('7dias')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === '7dias' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Últimos 7 días</button>
                <button type="button" onclick="aplicarPreset('15dias')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === '15dias' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Últimos 15 días</button>
                <button type="button" onclick="aplicarPreset('30dias')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === '30dias' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Últimos 30 días</button>
                <button type="button" onclick="aplicarPreset('este_mes')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === 'este_mes' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Este mes</button>
                <button type="button" onclick="aplicarPreset('mes_anterior')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === 'mes_anterior' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Mes anterior</button>
                <button type="button" onclick="aplicarPreset('anio_actual')" class="px-2.5 py-1 rounded-lg border text-xs font-semibold hover:bg-gray-50 {{ $periodo === 'anio_actual' ? 'bg-purple-100 border-purple-300 text-purple-900' : 'bg-white' }}">Año actual</button>
            </div>
        </form>
    </div>

    {{-- CONTEXTO ACTIVO (BANDEJA DEL PROVEEDOR O CONSOLIDADO) --}}
    <div class="mb-6 rounded-2xl p-5 border flex flex-col md:flex-row md:items-center justify-between gap-4"
         style="background: {{ $proveedorActual ? '#faf5ff' : '#f0fdf4' }}; border-color: {{ $proveedorActual ? '#e9d5ff' : '#bbf7d0' }};">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-white text-lg shrink-0 shadow-sm"
                 style="background: {{ $proveedorActual ? '#71277a' : '#39a900' }};">
                {{ $proveedorActual ? strtoupper(substr($proveedorActual->nombre, 0, 2)) : '🌐' }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-base font-extrabold text-gray-900">
                        {{ $proveedorActual ? $proveedorActual->nombre : 'Vista Consolidada — Todos los Proveedores' }}
                    </h3>
                    @if($proveedorActual)
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white border border-purple-200 text-purple-800">
                            {{ $proveedorActual->codigo ?? 'PRV-'.$proveedorActual->id }}
                        </span>
                        @if($proveedorActual->tipo)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-white border text-gray-700">
                                {{ $proveedorActual->tipo }}
                            </span>
                        @endif
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            {{ $rankingProveedores->count() }} proveedores activos con ventas
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-600 mt-0.5">
                    @if($proveedorActual)
                        Contacto: {{ $proveedorActual->correo ?? 'Sin correo' }} · Tel: {{ $proveedorActual->telefono ?? 'Sin teléfono' }}
                    @else
                        Mostrando la totalidad de ventas comerciales generadas en la tienda Zajuna Campo.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 flex-wrap">
            <div class="px-3 py-1.5 rounded-xl bg-white border text-xs font-bold text-gray-700 flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>{{ $periodoTexto }}</span>
            </div>
            @if($proveedorActual)
                <div class="px-3 py-1.5 rounded-xl text-xs font-bold bg-purple-700 text-white shadow-xs">
                    {{ $porcentajeParticipacion }}% de las ventas de la tienda
                </div>
            @endif
        </div>
    </div>

    {{-- KPIS PRINCIPALES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        {{-- Total Ingresos --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-6 h-6" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/><path d="M9 12h3M12 9v3"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5 text-gray-400">Total Ingresos</p>
                <p class="text-xl font-extrabold text-gray-900">${{ number_format($totalIngresos, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varIngresos >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varIngresos >= 0 ? '↑' : '↓' }} {{ abs($varIngresos) }}% vs. período anterior
                </p>
            </div>
        </div>

        {{-- Unidades Vendidas --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-6 h-6" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5 text-gray-400">Unidades Vendidas</p>
                <p class="text-xl font-extrabold text-gray-900">{{ number_format($totalUnidades, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varUnidades >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varUnidades >= 0 ? '↑' : '↓' }} {{ abs($varUnidades) }}% vs. período anterior
                </p>
            </div>
        </div>

        {{-- Órdenes / Transacciones --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                    <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5 text-gray-400">Transacciones</p>
                <p class="text-xl font-extrabold text-gray-900">{{ $totalOrdenes }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varOrdenes >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varOrdenes >= 0 ? '↑' : '↓' }} {{ abs($varOrdenes) }}% vs. período anterior
                </p>
            </div>
        </div>

        {{-- Ticket Promedio --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;">
                <svg class="w-6 h-6" style="color:#2563eb;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5 text-gray-400">Promedio por Venta</p>
                <p class="text-xl font-extrabold text-gray-900">${{ number_format($promedioVenta, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Por orden con producto</p>
            </div>
        </div>

    </div>

    {{-- GRÁFICA DE EVOLUCIÓN + TOP PRODUCTOS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Gráfica de Evolución Temporal --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900">Evolución de Ventas</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $esPorDia ? 'Ventas diarias generadas en el rango de fechas' : 'Ventas mensuales consolidadas' }}
                        </p>
                    </div>
                    {{-- Leyenda y escala --}}
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md" style="background:#71277a;"></span>
                            <span class="font-semibold text-gray-600">Ingresos ($ COP)</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full font-extrabold text-[11px] bg-purple-50 text-purple-800">
                            Pico: ${{ number_format($maxGraficoTotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Barras interactivas --}}
                @if($puntosGrafico->count() > 0 && $maxGraficoTotal > 0)
                <div class="flex items-end gap-1.5 sm:gap-2 pt-6 pb-2 overflow-x-auto" style="min-height:200px;">
                    @foreach($puntosGrafico as $pt)
                    @php
                        $pctTotal = round(($pt['total'] / $maxGraficoTotal) * 100);
                        $barHeight = max(6, $pctTotal * 1.5);
                        $isHigh = $pt['total'] == $puntosGrafico->max('total') && $pt['total'] > 0;
                    @endphp
                    <div class="flex-1 min-w-[28px] sm:min-w-[36px] flex flex-col items-center gap-1 group relative justify-end" style="height:190px;">
                        
                        {{-- Tooltip flotante al pasar el mouse --}}
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 z-20 pointer-events-none
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-150
                                    bg-gray-900 text-white rounded-xl px-3 py-2 text-[11px] font-semibold
                                    shadow-2xl whitespace-nowrap" style="min-width:120px;">
                            <p class="font-bold text-amber-300">{{ $pt['label'] }}</p>
                            <p class="mt-0.5">💰 ${{ number_format($pt['total'], 0, ',', '.') }} COP</p>
                            <p>📦 {{ $pt['cantidad'] }} unidades</p>
                            <p>🧾 {{ $pt['ordenes'] }} ventas</p>
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full
                                        border-4 border-transparent" style="border-top-color:#111827;"></div>
                        </div>

                        {{-- Barra --}}
                        <div class="w-full rounded-t-xl transition-all duration-300 cursor-pointer hover:opacity-85"
                             style="height:{{ $barHeight }}px; background: {{ $isHigh ? '#fdc300' : ($pt['total'] > 0 ? '#71277a' : '#f3f0e8') }}; {{ $isHigh ? 'box-shadow: 0 -4px 10px rgba(253,195,0,0.5);' : '' }}">
                        </div>

                        {{-- Etiqueta de fecha --}}
                        <span class="text-[9px] sm:text-[10px] font-semibold text-gray-400 truncate max-w-full text-center">
                            {{ $pt['label'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-16 text-center">
                    <p class="text-sm font-semibold text-gray-400">Sin ventas registradas en el período seleccionado</p>
                    <p class="text-xs text-gray-300 mt-1">Prueba seleccionando un rango más amplio o restableciendo el filtro.</p>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-gray-400" style="border-color:#f3f0e8;">
                <span>Total acumulado: <strong class="text-gray-900">${{ number_format($totalIngresos, 0, ',', '.') }}</strong></span>
                <span>Transacciones registradas: <strong class="text-gray-900">{{ $totalOrdenes }}</strong></span>
            </div>
        </div>

        {{-- Top Productos Más Vendidos del Proveedor --}}
        <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-1.5">
                            <span class="text-amber-500">🏆</span>
                            <span>Top Productos</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Más vendidos en el período</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800">
                        Top {{ $topProductos->count() }}
                    </span>
                </div>

                @if($topProductos->count() > 0)
                <div class="space-y-3">
                    @foreach($topProductos as $idx => $tp)
                    @php
                        $pctUnits = round(($tp->total_unidades / $maxTopUnidades) * 100);
                        $badgeStyle = match($idx) {
                            0 => 'background:#fef3c7; color:#b45309; border-color:#fde68a;',
                            1 => 'background:#f1f5f9; color:#475569; border-color:#e2e8f0;',
                            2 => 'background:#ffedd5; color:#c2410c; border-color:#fed7aa;',
                            default => 'background:#fafafa; color:#6b7280; border-color:#e5e7eb;',
                        };
                        $rankIcon = match($idx) {
                            0 => '🥇',
                            1 => '🥈',
                            2 => '🥉',
                            default => '#' . ($idx + 1),
                        };
                    @endphp
                    <div class="p-3 rounded-2xl border transition hover:bg-gray-50/60" style="border-color:#f3f0e8;">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg text-xs font-black flex items-center justify-center border shrink-0" style="{{ $badgeStyle }}">
                                    {{ $rankIcon }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 truncate" title="{{ $tp->nombre }}">
                                        {{ $tp->nombre }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 truncate">
                                        {{ $tp->categoria ?? 'Sin categoría' }} · ${{ number_format($tp->precio, 0, ',', '.') }} c/u
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold block text-green-700">
                                    ${{ number_format($tp->total_ventas, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-500">
                                    {{ $tp->total_unidades }} uds.
                                </span>
                            </div>
                        </div>
                        <div class="w-full h-1.5 rounded-full overflow-hidden bg-gray-100">
                            <div class="h-full rounded-full transition-all duration-300"
                                 style="width:{{ max(8, $pctUnits) }}%; background: {{ $idx === 0 ? '#fdc300' : ($idx === 1 ? '#71277a' : '#39a900') }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-12 text-center text-gray-400">
                    <p class="text-xs font-semibold">Sin productos vendidos</p>
                    <p class="text-[10px] mt-0.5">No hay ítems para este proveedor en este rango</p>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-gray-400" style="border-color:#f3f0e8;">
                <span>Total Top:</span>
                <span class="font-extrabold text-gray-900">${{ number_format($topProductos->sum('total_ventas'), 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    {{-- SEGUNDA FILA: RANKING COMPARATIVO DE PROVEEDORES + CATEGORÍAS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        {{-- Ranking de Proveedores --}}
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Comparativa de Proveedores en el Período</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Volumen de ventas, facturación y participación de cada proveedor</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full font-bold bg-green-100 text-green-800">
                    {{ $rankingProveedores->count() }} proveedores
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b" style="border-color:#f3f0e8;">
                            <th class="text-left py-2.5 font-bold text-gray-400 uppercase tracking-wider">Proveedor</th>
                            <th class="text-center py-2.5 font-bold text-gray-400 uppercase tracking-wider">Unidades</th>
                            <th class="text-center py-2.5 font-bold text-gray-400 uppercase tracking-wider">Órdenes</th>
                            <th class="text-right py-2.5 font-bold text-gray-400 uppercase tracking-wider">Total Ventas</th>
                            <th class="text-left py-2.5 pl-4 font-bold text-gray-400 uppercase tracking-wider">Participación</th>
                            <th class="text-right py-2.5 font-bold text-gray-400 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rankingProveedores as $rp)
                        @php
                            $pctProv = round(($rp->total_ventas / $maxVentasProveedor) * 100);
                            $pctTotalTienda = $totalTiendaIngresos > 0 ? round(($rp->total_ventas / $totalTiendaIngresos) * 100, 1) : 0;
                            $isSelected = (string)$proveedorId === (string)$rp->proveedor_id;
                        @endphp
                        <tr class="hover:bg-purple-50/40 transition {{ $isSelected ? 'bg-purple-50/70 font-bold' : '' }}">
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-white text-[11px] shrink-0"
                                         style="background:{{ $isSelected ? '#71277a' : '#39a900' }};">
                                        {{ strtoupper(substr($rp->proveedor_nombre, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-gray-900">{{ $rp->proveedor_nombre }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $rp->codigo ?? 'PRV-'.$rp->proveedor_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-center font-bold text-gray-700">
                                {{ number_format($rp->total_unidades, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-center text-gray-600">
                                {{ $rp->total_ordenes }}
                            </td>
                            <td class="py-3 text-right font-extrabold text-gray-900">
                                ${{ number_format($rp->total_ventas, 0, ',', '.') }}
                            </td>
                            <td class="py-3 pl-4">
                                <div class="w-28 flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full overflow-hidden bg-gray-100">
                                        <div class="h-full rounded-full"
                                             style="width:{{ max(6, $pctProv) }}%; background:{{ $isSelected ? '#71277a' : '#39a900' }};"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-gray-500 w-8">{{ $pctTotalTienda }}%</span>
                                </div>
                            </td>
                            <td class="py-3 text-right">
                                @if(!$isSelected)
                                <a href="{{ route('superadmin.ventas.dashboard', array_merge(request()->all(), ['proveedor_id' => $rp->proveedor_id])) }}"
                                   class="px-2.5 py-1 rounded-lg text-[11px] font-bold border border-purple-200 text-purple-700 hover:bg-purple-100 transition inline-block">
                                    Filtrar
                                </a>
                                @else
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-purple-700 text-white inline-block">
                                    Activo
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                No se registraron ventas de proveedores en este rango de fechas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ventas por Categoría --}}
        <div class="lg:col-span-1 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900 mb-1">Ventas por Categoría</h3>
                <p class="text-xs text-gray-400 mb-4">Desglose de productos comercializados</p>

                @if($distribucionCategorias->count() > 0)
                <div class="space-y-3">
                    @foreach($distribucionCategorias as $idx => $cat)
                    @php
                        $pctCat = round(($cat->total / $totalCategorias) * 100);
                        $col = $catColors[$idx % count($catColors)];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold flex items-center gap-1.5 text-gray-700">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $col }};"></span>
                                {{ $cat->categoria }}
                            </span>
                            <span class="font-extrabold text-gray-900">${{ number_format($cat->total, 0, ',', '.') }} ({{ $pctCat }}%)</span>
                        </div>
                        <div class="w-full h-2 rounded-full overflow-hidden bg-gray-100">
                            <div class="h-full rounded-full transition-all duration-300" style="width:{{ $pctCat }}%; background:{{ $col }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-12 text-center text-gray-400">
                    <p class="text-xs font-semibold">Sin categorías registradas</p>
                </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t flex items-center justify-between text-xs text-gray-400" style="border-color:#f3f0e8;">
                <span>Total categorías:</span>
                <span class="font-extrabold text-gray-900">${{ number_format($distribucionCategorias->sum('total'), 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    {{-- TERCERA FILA: DETALLE DE TRANSACCIONES / VENTAS DEL PERÍODO --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b flex items-center justify-between" style="border-color:#f3f0e8;">
            <div>
                <h3 class="text-sm font-extrabold text-gray-900">Transacciones de Ventas en el Período</h3>
                <p class="text-xs text-gray-400 mt-0.5">Listado detallado de órdenes asociadas al filtro actual</p>
            </div>
            <span class="text-xs text-gray-500 font-semibold">
                Mostrando {{ $ventasRecientes->count() }} de {{ $ventasRecientes->total() }} órdenes
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b text-xs font-bold uppercase tracking-wider text-gray-400" style="border-color:#f3f0e8;">
                        <th class="text-left px-6 py-4"># Venta</th>
                        <th class="text-left px-4 py-4">Productos del Proveedor</th>
                        <th class="text-left px-4 py-4">Cliente</th>
                        <th class="text-center px-4 py-4">Fecha</th>
                        <th class="text-right px-4 py-4">Subtotal Proveedor</th>
                        <th class="text-right px-6 py-4">Total Venta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs">
                    @forelse($ventasRecientes as $v)
                    @php
                        // Filtrar sólo los items que pertenecen a este proveedor si está seleccionado
                        $itemsMostrados = $proveedorId
                            ? $v->items->filter(fn($it) => $it->producto && $it->producto->proveedor_id == $proveedorId)
                            : $v->items;
                        $subtotalProv = $itemsMostrados->sum('subtotal');
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-purple-100 text-purple-900">
                                #{{ $v->id }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="space-y-1">
                                @foreach($itemsMostrados as $item)
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-gray-800">{{ $item->cantidad }}x</span>
                                    <span class="text-gray-700">{{ $item->producto->nombre ?? 'Producto eliminado' }}</span>
                                    <span class="text-gray-400 text-[10px]">(${number_format($item->subtotal,0,',','.')})</span>
                                    @if(!$proveedorId && $item->producto && $item->producto->proveedor)
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-gray-100 text-gray-600 font-semibold">
                                            {{ $item->producto->proveedor->nombre }}
                                        </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-4 font-semibold text-gray-700">
                            {{ $v->cliente ?: 'Consumidor Final' }}
                        </td>
                        <td class="px-4 py-4 text-center text-gray-500 whitespace-nowrap">
                            {{ $v->fechaVenta ? $v->fechaVenta->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="px-4 py-4 text-right font-extrabold text-green-700 text-sm whitespace-nowrap">
                            ${{ number_format($subtotalProv, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900 whitespace-nowrap">
                            ${{ number_format($v->total, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No se encontraron transacciones para el proveedor y rango de fechas seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ventasRecientes->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50" style="border-color:#f3f0e8;">
            {{ $ventasRecientes->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function toggleFechasDashboard(val) {
    const divIni = document.getElementById('divFechaInicio');
    const divFin = document.getElementById('divFechaFin');
    if (val === 'rango') {
        divIni.classList.remove('hidden');
        divFin.classList.remove('hidden');
    } else {
        divIni.classList.add('hidden');
        divFin.classList.add('hidden');
    }
}

function aplicarPreset(preset) {
    const sel = document.getElementById('selectPeriodo');
    sel.value = preset;
    toggleFechasDashboard(preset);
    document.getElementById('formFiltroDashboard').submit();
}
</script>
</x-superadmin-layout>

