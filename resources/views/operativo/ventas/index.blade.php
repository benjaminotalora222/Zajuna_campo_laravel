@php $title = 'Ventas'; @endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- SUB-NAVEGACIÓN / TABS DE VENTAS --}}
    <div class="flex items-center gap-2 mb-6 border-b pb-4" style="border-color:#f3f0e8;">
        <a href="{{ route('operativo.ventas.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm transition"
           style="background:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span>Registro y Transacciones</span>
        </a>
        <a href="{{ route('operativo.ventas.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold border transition hover:bg-gray-50"
           style="border-color:#e7e0cc; color:#5a5a4f; background:#fff;">
            <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span>Dashboard por Proveedor</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#fdc300] text-[#71277a]">BI</span>
        </a>
    </div>

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Ventas</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Registro y seguimiento de todas las transacciones.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nueva Venta
        </button>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <div class="flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @if(session('venta_creada_id'))
        <a href="{{ route('operativo.ventas.facturaPos', session('venta_creada_id')) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90 shrink-0"
           style="background:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            <span>Imprimir Factura POS #{{ str_pad(session('venta_creada_id'), 6, '0', STR_PAD_LEFT) }}</span>
        </a>
        @endif
    </div>
    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#71277a;">Ventas hoy</p>
            <p class="text-2xl font-extrabold mb-1" style="color:#1c2b16;">{{ $stats['total_hoy'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">${{ number_format($stats['ingresos_hoy'],0,',','.') }} hoy</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#71277a;">Ventas del mes</p>
            <p class="text-2xl font-extrabold mb-1" style="color:#1c2b16;">{{ $stats['total_mes'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">{{ now()->locale('es')->isoFormat('MMMM YYYY') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#71277a;">Ingresos del mes</p>
            <p class="text-2xl font-extrabold mb-1" style="color:#1c2b16;">${{ number_format($stats['ingresos_mes'],0,',','.') }}</p>
            <p class="text-xs" style="color:#9a9a8a;">COP este mes</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#71277a;">Promedio por venta</p>
            <p class="text-2xl font-extrabold mb-1" style="color:#1c2b16;">
                ${{ $stats['total_mes'] > 0 ? number_format($stats['ingresos_mes']/$stats['total_mes'],0,',','.') : '0' }}
            </p>
            <p class="text-xs" style="color:#9a9a8a;">Por transacción</p>
        </div>
    </div>

    {{-- GRÁFICO DE VENTAS + PRODUCTOS MÁS VENDIDOS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Gráfico de ventas --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                {{-- Encabezado --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-sm font-extrabold" style="color:#1c2b16;">Ventas últimos 6 meses</h3>
                        <p class="text-xs mt-0.5" style="color:#9a9a8a;">Transacciones e ingresos por mes</p>
                    </div>
                    {{-- Leyenda --}}
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm" style="background:linear-gradient(180deg,#39a900,#2d8600);"></div>
                            <span class="text-xs font-semibold" style="color:#5a5a4f;">Transacciones</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm" style="background:linear-gradient(180deg,#fdc300,#e6b000);"></div>
                            <span class="text-xs font-semibold" style="color:#5a5a4f;">Ingresos</span>
                        </div>
                    </div>
                </div>

                @php
                    $maxV  = $ventasMes->max('cantidad') ?: 1;
                    $maxI  = $ventasMes->max('ingresos') ?: 1;
                    $totalTx      = $ventasMes->sum('cantidad');
                    $totalIngresos = $ventasMes->sum('ingresos');
                    $mesActivo    = $ventasMes->last();
                @endphp

                {{-- Resumen rápido --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="rounded-xl px-4 py-3" style="background:#f0fdf4; border:1px solid #dcfce7;">
                        <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#166534;">Total período</p>
                        <p class="text-xl font-extrabold" style="color:#166534;">{{ $totalTx }}</p>
                        <p class="text-[11px]" style="color:#4ade80;">transacciones</p>
                    </div>
                    <div class="rounded-xl px-4 py-3" style="background:#fffbeb; border:1px solid #fef08a;">
                        <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#854d0e;">Ingresos período</p>
                        <p class="text-xl font-extrabold" style="color:#854d0e;">${{ $totalIngresos >= 1000000 ? number_format($totalIngresos/1000000,1).'M' : number_format($totalIngresos,0,',','.') }}</p>
                        <p class="text-[11px]" style="color:#ca8a04;">COP acumulado</p>
                    </div>
                    <div class="rounded-xl px-4 py-3" style="background:#faf5ff; border:1px solid #e9d5ff;">
                        <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color:#6b21a8;">Mes actual</p>
                        <p class="text-xl font-extrabold" style="color:#71277a;">{{ $mesActivo['cantidad'] ?? 0 }}</p>
                        <p class="text-[11px]" style="color:#a855f7;">{{ $mesActivo['mes'] ?? '' }}</p>
                    </div>
                </div>

                {{-- Barras --}}
                <div class="flex items-end gap-2" style="height:140px;">
                    @foreach($ventasMes as $idx => $vm)
                    @php
                        $pctTx = round(($vm['cantidad'] / $maxV) * 100);
                        $pctIn = round((($vm['ingresos'] ?? 0) / $maxI) * 100);
                        $heightTx = max(4, $pctTx * 1.2);
                        $heightIn = max(4, $pctIn * 1.2);
                        $isLast = $loop->last;
                        $colorTx = $isLast ? 'linear-gradient(180deg,#39a900,#2d8600)' : ($pctTx >= 70 ? 'linear-gradient(180deg,#5fcc1a,#39a900)' : ($pctTx >= 30 ? 'linear-gradient(180deg,#a3e635,#6bbd00)' : 'linear-gradient(180deg,#d1fae5,#bbf7d0)'));
                        $colorIn = $isLast ? 'linear-gradient(180deg,#fdc300,#e6b000)' : ($pctIn >= 70 ? 'linear-gradient(180deg,#fde047,#fdc300)' : ($pctIn >= 30 ? 'linear-gradient(180deg,#fef08a,#fdc300)' : 'linear-gradient(180deg,#fef9c3,#fef08a)'));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group relative" style="height:140px; justify-content:flex-end;">

                        {{-- Tooltip --}}
                        <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 z-10 pointer-events-none
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-200
                                    bg-gray-900 text-white rounded-xl px-3 py-2 text-[11px] font-semibold
                                    shadow-xl whitespace-nowrap" style="min-width:110px;">
                            <p class="font-bold mb-0.5" style="color:#fdc300;">{{ $vm['mes'] }}</p>
                            <p>🛒 {{ $vm['cantidad'] }} venta{{ $vm['cantidad'] != 1 ? 's' : '' }}</p>
                            <p>💰 ${{ number_format($vm['ingresos'] ?? 0, 0, ',', '.') }}</p>
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-full
                                        border-4 border-transparent" style="border-top-color:#111827;"></div>
                        </div>

                        {{-- Par de barras --}}
                        <div class="flex items-end gap-0.5 w-full px-1" style="height:120px;">
                            {{-- Barra transacciones --}}
                            <div class="flex-1 rounded-t-lg transition-all duration-500 cursor-pointer hover:opacity-80"
                                 style="height:{{ $heightTx }}px; background:{{ $colorTx }}; {{ $isLast ? 'box-shadow:0 -3px 8px rgba(57,169,0,0.4);' : '' }}">
                            </div>
                            {{-- Barra ingresos --}}
                            <div class="flex-1 rounded-t-lg transition-all duration-500 cursor-pointer hover:opacity-80"
                                 style="height:{{ $heightIn }}px; background:{{ $colorIn }}; {{ $isLast ? 'box-shadow:0 -3px 8px rgba(253,195,0,0.4);' : '' }}">
                            </div>
                        </div>

                        {{-- Etiqueta valor --}}
                        <div class="text-center leading-tight">
                            @if($vm['cantidad'] > 0)
                                <span class="text-[10px] font-extrabold block" style="color:{{ $isLast ? '#39a900' : '#71277a' }};">{{ $vm['cantidad'] }}</span>
                            @else
                                <span class="text-[10px] font-bold block" style="color:#d1d5db;">0</span>
                            @endif
                        </div>

                        {{-- Mes --}}
                        <span class="text-[10px] font-semibold" style="color:{{ $isLast ? '#1c2b16' : '#9a9a8a' }};">{{ $vm['mes'] }}</span>

                        {{-- Indicador mes activo --}}
                        @if($isLast)
                            <div class="w-1.5 h-1.5 rounded-full mt-0.5" style="background:#39a900;"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Línea de escala --}}
            <div class="mt-3 pt-3 border-t flex items-center justify-between" style="border-color:#f3f0e8;">
                <span class="text-[10px]" style="color:#d1d5db;">0</span>
                <span class="text-[10px]" style="color:#9a9a8a;">Máx: {{ $maxV }} transacciones · ${{ number_format($maxI,0,',','.') }}</span>
                <div class="w-1.5 h-1.5 rounded-full" style="background:#39a900;"></div>
            </div>
        </div>

        {{-- Top Productos Más Vendidos --}}
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
            <div>
                {{-- Encabezado --}}
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold flex items-center gap-1.5" style="color:#1c2b16;">
                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>Más Vendidos</span>
                        </h3>
                        <p class="text-xs mt-0.5" style="color:#9a9a8a;">
                            {{ request('mes') || request('anio') ? 'Filtrado por fecha' : 'Top artículos más vendidos' }}
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider" style="background:#faf5ff; color:#71277a;">
                        Top 5
                    </span>
                </div>

                {{-- Lista de productos --}}
                @if(isset($topProductos) && $topProductos->count() > 0)
                <div class="space-y-3">
                    @foreach($topProductos as $idx => $tp)
                    @php
                        $pctUnits = round(($tp->total_unidades / $maxUnidadesTop) * 100);
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
                    <div class="p-2.5 rounded-xl border transition hover:bg-gray-50/60" style="border-color:#f3f0e8;">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg text-xs font-black flex items-center justify-center border shrink-0" style="{{ $badgeStyle }}">
                                    {{ $rankIcon }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold truncate" style="color:#1c2b16;" title="{{ $tp->nombre }}">
                                        {{ $tp->nombre }}
                                    </p>
                                    @if(!empty($tp->categoria))
                                    <span class="text-[10px] px-1.5 py-0.2 rounded" style="background:#f4f1ea; color:#7a7a6e;">
                                        {{ $tp->categoria }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-xs font-extrabold block" style="color:#39a900;">
                                    ${{ number_format($tp->total_ventas, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-semibold" style="color:#5a5a4f;">
                                    {{ $tp->total_unidades }} {{ $tp->total_unidades == 1 ? 'ud.' : 'uds.' }}
                                </span>
                            </div>
                        </div>
                        {{-- Barra de volumen --}}
                        <div class="w-full h-1.5 rounded-full overflow-hidden" style="background:#f3f0e8;">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width:{{ max(6, $pctUnits) }}%; background:{{ $idx === 0 ? '#fdc300' : ($idx === 1 ? '#71277a' : '#39a900') }};"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-8 text-center">
                    <div class="w-10 h-10 rounded-full mx-auto flex items-center justify-center mb-2" style="background:#f4f1ea;">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-gray-500">Sin ventas registradas</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Aparecerán aquí al registrar ventas</p>
                </div>
                @endif
            </div>

            {{-- Pie de tarjeta --}}
            <div class="mt-3 pt-3 border-t flex items-center justify-between text-[11px]" style="border-color:#f3f0e8;">
                <span style="color:#9a9a8a;">Total Top 5:</span>
                <span class="font-extrabold" style="color:#1c2b16;">${{ number_format($totalVentasTop ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('operativo.ventas.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cliente o producto..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Proveedor</label>
                <select name="proveedor_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}" {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[120px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Mes</label>
                <select name="mes" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->locale('es')->isoFormat('MMMM') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[100px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Año</label>
                <select name="anio" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    @foreach($anios as $a)
                        <option value="{{ $a }}" {{ request('anio') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                    @if($anios->isEmpty())
                        <option value="{{ now()->year }}" selected>{{ now()->year }}</option>
                    @endif
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','mes','anio','proveedor_id']))
            <a href="{{ route('operativo.ventas.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:2px solid #f3f0e8;">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">#</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Productos</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Cliente</th>
                    <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Ítems</th>
                    <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Total</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Fecha</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $v)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold px-2 py-1 rounded-lg" style="background:#f3e8ff; color:#71277a;">#{{ $v->id }}</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="space-y-1">
                            @foreach($v->items->take(3) as $item)
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:#39a900;"></span>
                                <span class="text-xs" style="color:#1c2b16;">
                                    {{ $item->producto->nombre ?? '—' }}
                                    <span style="color:#9a9a8a;">× {{ $item->cantidad }}</span>
                                </span>
                                @if(isset($item->porcentaje_iva) && $item->porcentaje_iva > 0)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md" style="background:#f3e8ff; color:#71277a; border: 1px solid #e9d5ff;">
                                        +{{ round($item->porcentaje_iva) }}% IVA
                                    </span>
                                @endif
                            </div>
                            @endforeach
                            @if($v->items->count() > 3)
                            <span class="text-xs font-semibold block mt-0.5" style="color:#71277a;">+{{ $v->items->count() - 3 }} más...</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm" style="color:#5a5a4f;">{{ $v->cliente ?: '—' }}</td>
                    <td class="px-4 py-4 text-right">
                        <span class="text-xs font-bold px-2 py-1 rounded-full" style="background:#f0fdf4; color:#166534;">
                            {{ $v->items->count() }} ítem{{ $v->items->count() != 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="font-extrabold text-sm block" style="color:#39a900;">
                            ${{ number_format($v->total, 0, ',', '.') }}
                        </span>
                        @if(isset($v->iva) && $v->iva > 0)
                            <span class="text-[11px] font-semibold block text-purple-700">
                                (IVA: ${{ number_format($v->iva, 0, ',', '.') }})
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($v->fechaVenta)
                        <p class="text-xs font-semibold" style="color:#1c2b16;">{{ \Carbon\Carbon::parse($v->fechaVenta)->locale('es')->isoFormat('D MMM YYYY') }}</p>
                        <p class="text-xs" style="color:#9a9a8a;">{{ \Carbon\Carbon::parse($v->fechaVenta)->diffForHumans() }}</p>
                        @else —
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('operativo.ventas.facturaPos', $v) }}" target="_blank"
                               class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                               style="background:#f3e8ff; color:#71277a;" title="Factura POS (Imprimir / Descargar)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="6 9 6 2 18 2 18 9"/>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                    <rect x="6" y="14" width="12" height="8"/>
                                </svg>
                            </a>
                            <button onclick='abrirModalEditar(@json($v->load("items.producto")))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>
                            <button onclick="abrirModalEliminar({{ $v->id }})"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#fef2f2; color:#ef4444;" title="Eliminar">
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
                            <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                            <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                        </svg>
                        <p class="text-sm font-semibold">No hay ventas registradas</p>
                        <p class="text-xs mt-1">Registra la primera venta con el botón "Nueva Venta".</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($ventas->count())
            <tfoot>
                <tr style="border-top:2px solid #f3f0e8; background:#fdf9ee;">
                    <td colspan="4" class="px-6 py-3 text-xs font-bold text-right" style="color:#5a5a4f;">Total en esta página:</td>
                    <td class="px-4 py-3 text-right font-extrabold" style="color:#39a900;">${{ number_format($ventas->sum('total'),0,',','.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>

        {{-- Paginación --}}
        @if($ventas->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} ventas</p>
            <div class="flex items-center gap-1">
                @if($ventas->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></span>
                @else
                    <a href="{{ $ventas->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></a>
                @endif
                @foreach($ventas->getUrlRange(max(1,$ventas->currentPage()-2),min($ventas->lastPage(),$ventas->currentPage()+2)) as $page => $url)
                    @if($page == $ventas->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                    @endif
                @endforeach
                @if($ventas->hasMorePages())
                    <a href="{{ $ventas->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></span>
                @endif
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">Mostrando {{ $ventas->count() }} venta{{ $ventas->count()!=1?'s':'' }}</div>
        @endif
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR (multi-ítem con IVA) ══════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-xs" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-xs" style="background:#f0fdf4; border: 1px solid #bbf7d0;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                        <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </div>
                <div>
                    <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nueva Venta</h2>
                    <p class="text-xs text-gray-500">Agrega productos, define cantidades, precios e impuestos (IVA).</p>
                </div>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formVenta" method="POST" class="px-7 py-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <p class="font-bold mb-1">Corrige los errores:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Info general --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Cliente</label>
                    <input type="text" name="cliente" id="inputCliente" placeholder="Nombre del cliente" maxlength="255"
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\.\'\(\)]+$"
                           title="Solo letras y espacios (no se permiten números)"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\.\'\(\)]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900'" onblur="this.style.borderColor='#e7e0cc'">
                    <p class="text-[11px] mt-1 text-gray-400">Solo letras y espacios (sin números).</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="fechaVenta" id="inputFechaVenta" required value="{{ now()->toDateString() }}"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900'" onblur="this.style.borderColor='#e7e0cc'">
                </div>
            </div>

            {{-- ── ÍTEMS ── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="text-sm font-bold block" style="color:#1c2b16;">
                            Productos y Detalle de Venta <span style="color:#ef4444;">*</span>
                        </label>
                        <span class="text-xs text-gray-400">Puedes asignar la tarifa de IVA a cada producto individualmente</span>
                    </div>
                    <button type="button" onclick="agregarItem()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition hover:opacity-90 shadow-xs"
                            style="background:#fdc300; color:#71277a;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                        </svg>
                        Agregar producto
                    </button>
                </div>

                {{-- Cabecera de la tabla de ítems --}}
                <div class="hidden sm:grid grid-cols-12 gap-2.5 mb-2 px-3 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">
                    <div class="col-span-4">Producto</div>
                    <div class="col-span-2 text-center">Cant.</div>
                    <div class="col-span-2 text-right">Precio Unit.</div>
                    <div class="col-span-2 text-center">IVA (%)</div>
                    <div class="col-span-2 text-right">Total Ítem</div>
                </div>

                <div id="itemsContainer" class="space-y-2.5"></div>

                <p id="itemsVacioMsg" class="text-center py-6 text-sm" style="color:#9a9a8a;">
                    Haz clic en "Agregar producto" para añadir ítems a la venta.
                </p>
            </div>

            {{-- Resumen de totales con IVA --}}
            <div class="rounded-2xl p-4 space-y-2 border" style="background:#f9fdf7; border-color:#86efac;">
                <div class="flex items-center justify-between text-xs" style="color:#5a5a4f;">
                    <span>Subtotal neto (sin IVA):</span>
                    <span id="subtotalDisplay" class="font-bold text-sm" style="color:#1c2b16;">$0</span>
                </div>
                <div class="flex items-center justify-between text-xs pb-2 border-b" style="border-color:#dcfce7; color:#5a5a4f;">
                    <span class="flex items-center gap-1.5">
                        <span>IVA total:</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700">Impuesto</span>
                    </span>
                    <span id="ivaDisplay" class="font-bold text-sm text-purple-700">+$0</span>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider" style="color:#166534;">Total a pagar</p>
                        <p class="text-[11px]" style="color:#9a9a8a;" id="totalItemsCount">0 productos agregados</p>
                    </div>
                    <p id="totalDisplay" class="text-2xl font-extrabold" style="color:#166534;">$0</p>
                </div>
            </div>

            {{-- Observaciones --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Observaciones</label>
                <textarea name="observaciones" id="inputObservaciones" rows="2" placeholder="Notas adicionales..."
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none transition"
                          style="border-color:#e7e0cc; background:#fafafa;"
                          onfocus="this.style.borderColor='#39a900'" onblur="this.style.borderColor='#e7e0cc'"></textarea>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90 transition shadow-sm cursor-pointer"
                        style="background:#39a900;">
                    Registrar venta
                </button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50 transition cursor-pointer"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ELIMINAR ══════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-xs" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar venta?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">El stock de todos los productos será restaurado automáticamente.</p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90 transition cursor-pointer" style="background:#ef4444;">Sí, eliminar</button>
                    <button type="button" onclick="cerrarModal('modalEliminar')" class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50 transition cursor-pointer" style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Datos de productos para JS --}}
@php
$productosJs = $productos->map(function($p) {
    return [
        'id'     => $p->id,
        'nombre' => $p->nombre,
        'precio' => (float)$p->precio,
        'unidad' => $p->unidad,
        'stock'  => $p->stock_calculado,
        'iva'    => (float)($p->iva ?? 0),
    ];
})->values()->toJson();
@endphp
<script>
const PRODUCTOS = {!! $productosJs !!};

let itemIndex = 0;

function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}
['modalForm','modalEliminar'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', function(e) {
            if (e.target === this) cerrarModal(id);
        });
    }
});

function buildSelectOptions(selectedId = null) {
    let opts = '<option value="">Selecciona producto...</option>';
    PRODUCTOS.forEach(p => {
        opts += `<option value="${p.id}" data-precio="${p.precio}" data-unidad="${p.unidad || ''}" data-stock="${p.stock}" data-iva="${p.iva ?? 0}" ${p.id == selectedId ? 'selected' : ''}>${p.nombre} (Stock: ${p.stock ?? 0} ${p.unidad ?? ''})</option>`;
    });
    return opts;
}

function setIvaValue(i, val) {
    const num = (val !== null && val !== undefined && val !== '') ? parseFloat(val) : 0;
    const select = document.getElementById(`ivaSelect_${i}`);
    const customWrap = document.getElementById(`ivaCustomWrap_${i}`);
    const customInput = document.getElementById(`ivaCustomInput_${i}`);
    const hiddenInput = document.getElementById(`porcentajeIvaFinal_${i}`);

    if (!select || !hiddenInput) return;

    if ([0, 5, 19].includes(num)) {
        select.value = String(num);
        if (customWrap) customWrap.classList.add('hidden');
        if (customInput) customInput.value = '';
        hiddenInput.value = num;
    } else {
        select.value = 'custom';
        if (customWrap) customWrap.classList.remove('hidden');
        if (customInput) customInput.value = num;
        hiddenInput.value = num;
    }
}

function onIvaSelectChange(i) {
    const select = document.getElementById(`ivaSelect_${i}`);
    const customWrap = document.getElementById(`ivaCustomWrap_${i}`);
    const customInput = document.getElementById(`ivaCustomInput_${i}`);
    const hiddenInput = document.getElementById(`porcentajeIvaFinal_${i}`);

    if (!select || !hiddenInput) return;

    if (select.value === 'custom') {
        if (customWrap) customWrap.classList.remove('hidden');
        if (customInput) {
            customInput.focus();
            hiddenInput.value = parseFloat(customInput.value) || 0;
        }
    } else {
        if (customWrap) customWrap.classList.add('hidden');
        if (customInput) customInput.value = '';
        hiddenInput.value = parseFloat(select.value) || 0;
    }
    recalcularTotal();
}

function onCustomIvaInput(i) {
    const customInput = document.getElementById(`ivaCustomInput_${i}`);
    const hiddenInput = document.getElementById(`porcentajeIvaFinal_${i}`);
    if (!customInput || !hiddenInput) return;

    let val = parseFloat(customInput.value);
    if (isNaN(val) || val < 0) val = 0;
    if (val > 100) {
        val = 100;
        customInput.value = 100;
    }
    hiddenInput.value = val;
    recalcularTotal();
}

function agregarItem(idProducto = null, cantidad = 1, precio = '', porcentajeIva = null) {
    const i = itemIndex++;
    const container = document.getElementById('itemsContainer');
    document.getElementById('itemsVacioMsg').classList.add('hidden');

    const row = document.createElement('div');
    row.id = `item_${i}`;
    row.className = 'grid grid-cols-12 gap-2.5 items-start p-3 rounded-xl border transition';
    row.style = 'border-color:#f3f0e8; background:#fafafa;';
    row.innerHTML = `
        <div class="col-span-12 sm:col-span-4">
            <label class="block text-[11px] font-bold text-gray-500 sm:hidden mb-1">Producto</label>
            <select name="items[${i}][idProducto]" required
                    class="w-full px-3 py-2 rounded-lg border text-xs outline-none bg-white font-medium"
                    style="border-color:#e7e0cc;"
                    onchange="onProductoChange(this, ${i})">
                ${buildSelectOptions(idProducto)}
            </select>
            <p id="stock_${i}" class="text-[11px] mt-1 font-semibold hidden" style="color:#39a900;"></p>
        </div>
        <div class="col-span-4 sm:col-span-2">
            <label class="block text-[11px] font-bold text-gray-500 sm:hidden mb-1">Cantidad</label>
            <input type="number" name="items[${i}][cantidad]" id="cant_${i}" value="${cantidad}" min="1" max="999999" required
                   class="w-full px-2 py-2 rounded-lg border text-xs outline-none text-center bg-white font-medium"
                   style="border-color:#e7e0cc;"
                   onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                   oninput="this.value = this.value.replace(/\\D/g, ''); recalcularTotal()" placeholder="1">
        </div>
        <div class="col-span-8 sm:col-span-2">
            <label class="block text-[11px] font-bold text-gray-500 sm:hidden mb-1">Precio Unit.</label>
            <div class="relative">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">$</span>
                <input type="number" name="items[${i}][precioUnitario]" id="precio_${i}" value="${precio}" min="0" max="99999999.99" step="0.01" required
                       class="w-full pl-6 pr-2 py-2 rounded-lg border text-xs outline-none text-right bg-white font-medium"
                       style="border-color:#e7e0cc;"
                       onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();"
                       oninput="recalcularTotal()" placeholder="0">
            </div>
        </div>
        <div class="col-span-6 sm:col-span-2">
            <label class="block text-[11px] font-bold text-gray-500 sm:hidden mb-1">IVA (%)</label>
            <input type="hidden" name="items[${i}][porcentaje_iva]" id="porcentajeIvaFinal_${i}" value="0">
            <select id="ivaSelect_${i}" onchange="onIvaSelectChange(${i})"
                    class="w-full px-2 py-2 rounded-lg border text-xs outline-none bg-white font-medium cursor-pointer"
                    style="border-color:#e7e0cc;">
                <option value="0">0% (Sin IVA)</option>
                <option value="5">5%</option>
                <option value="19">19% (General)</option>
                <option value="custom">Otro %</option>
            </select>
            <div id="ivaCustomWrap_${i}" class="mt-1 hidden">
                <div class="relative">
                    <input type="number" id="ivaCustomInput_${i}" min="0" max="100" step="0.1"
                           placeholder="%"
                           class="w-full pl-2 pr-5 py-1 rounded-lg border text-xs text-right bg-white outline-none"
                           style="border-color:#e7e0cc;"
                           oninput="onCustomIvaInput(${i})">
                    <span class="absolute right-1.5 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 font-bold">%</span>
                </div>
            </div>
        </div>
        <div class="col-span-6 sm:col-span-2 flex items-center justify-between gap-1 pt-1 sm:pt-0">
            <div class="text-right flex-1 min-w-0 pr-1">
                <div id="subtotalLinea_${i}" class="text-xs font-bold truncate" style="color:#1c2b16;">$0</div>
                <div id="subtotalIvaNota_${i}" class="text-[10px] font-medium truncate text-gray-400">Sin IVA</div>
            </div>
            <button type="button" onclick="eliminarItem(${i})"
                    class="w-8 h-8 rounded-lg flex items-center justify-center hover:scale-110 transition shrink-0"
                    style="background:#fef2f2; color:#ef4444;" title="Eliminar ítem">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    container.appendChild(row);

    // Determinar IVA inicial
    let ivaInicial = 0;
    if (porcentajeIva !== null && porcentajeIva !== undefined) {
        ivaInicial = porcentajeIva;
    } else if (idProducto) {
        const prod = PRODUCTOS.find(p => p.id == idProducto);
        if (prod && prod.iva !== undefined) {
            ivaInicial = prod.iva;
        }
    }
    setIvaValue(i, ivaInicial);

    // Si viene con producto precargado, mostrar stock y precio
    if (idProducto) {
        const sel = row.querySelector('select');
        onProductoChange(sel, i, false);
        if (precio !== '') {
            row.querySelector(`input[name="items[${i}][precioUnitario]"]`).value = precio;
        }
    }

    recalcularTotal();
}

function onProductoChange(sel, i, updateIva = true) {
    const opt   = sel.options[sel.selectedIndex];
    const precio = opt ? opt.getAttribute('data-precio') : '';
    const stock  = opt ? opt.getAttribute('data-stock') : null;
    const unidad = opt ? opt.getAttribute('data-unidad') : '';
    const iva    = opt ? opt.getAttribute('data-iva') : 0;
    const row    = document.getElementById(`item_${i}`);

    if (precio && row) {
        row.querySelector(`input[name="items[${i}][precioUnitario]"]`).value = precio;
    }

    if (updateIva && opt && opt.value) {
        setIvaValue(i, parseFloat(iva) || 0);
    }

    const stockEl = document.getElementById(`stock_${i}`);
    if (stockEl && stock !== null && sel.value) {
        stockEl.textContent = `Stock: ${stock} ${unidad}`;
        stockEl.classList.remove('hidden');
        stockEl.style.color = parseInt(stock) > 0 ? '#39a900' : '#ef4444';
    } else if (stockEl) {
        stockEl.classList.add('hidden');
    }

    recalcularTotal();
}

function eliminarItem(i) {
    const el = document.getElementById(`item_${i}`);
    if (el) el.remove();
    if (document.getElementById('itemsContainer').children.length === 0) {
        document.getElementById('itemsVacioMsg').classList.remove('hidden');
    }
    recalcularTotal();
}

function recalcularTotal() {
    let subtotalNeto = 0;
    let totalIva = 0;
    let totalPagar = 0;
    let count = 0;

    document.querySelectorAll('#itemsContainer > div').forEach(row => {
        const i = row.id.replace('item_', '');
        const cant = parseFloat(document.getElementById(`cant_${i}`)?.value) || 0;
        const precio = parseFloat(document.getElementById(`precio_${i}`)?.value) || 0;
        const pctIva = parseFloat(document.getElementById(`porcentajeIvaFinal_${i}`)?.value) || 0;

        const baseLinea = Math.round(cant * precio * 100) / 100;
        const ivaLinea = Math.round(baseLinea * (pctIva / 100) * 100) / 100;
        const totalLinea = Math.round((baseLinea + ivaLinea) * 100) / 100;

        const subtotalLineaEl = document.getElementById(`subtotalLinea_${i}`);
        const subtotalIvaNotaEl = document.getElementById(`subtotalIvaNota_${i}`);

        if (subtotalLineaEl) {
            subtotalLineaEl.textContent = '$' + Math.round(totalLinea).toLocaleString('es-CO');
        }
        if (subtotalIvaNotaEl) {
            if (pctIva > 0) {
                subtotalIvaNotaEl.textContent = `+IVA (${pctIva}%): $` + Math.round(ivaLinea).toLocaleString('es-CO');
                subtotalIvaNotaEl.className = 'text-[10px] font-semibold truncate text-purple-700';
            } else {
                subtotalIvaNotaEl.textContent = 'Sin IVA (0%)';
                subtotalIvaNotaEl.className = 'text-[10px] font-medium truncate text-gray-400';
            }
        }

        subtotalNeto += baseLinea;
        totalIva += ivaLinea;
        totalPagar += totalLinea;
        if (cant > 0) count++;
    });

    const subtotalEl = document.getElementById('subtotalDisplay');
    const ivaEl = document.getElementById('ivaDisplay');
    const totalEl = document.getElementById('totalDisplay');
    const countEl = document.getElementById('totalItemsCount');

    if (subtotalEl) subtotalEl.textContent = '$' + Math.round(subtotalNeto).toLocaleString('es-CO');
    if (ivaEl) ivaEl.textContent = '+$' + Math.round(totalIva).toLocaleString('es-CO');
    if (totalEl) totalEl.textContent = '$' + Math.round(totalPagar).toLocaleString('es-CO');
    if (countEl) countEl.textContent = `${count} producto${count !== 1 ? 's' : ''} agregado${count !== 1 ? 's' : ''}`;
}

function limpiarItems() {
    document.getElementById('itemsContainer').innerHTML = '';
    document.getElementById('itemsVacioMsg').classList.remove('hidden');
    itemIndex = 0;
    recalcularTotal();
}

function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nueva Venta';
    document.getElementById('btnGuardar').textContent     = 'Registrar venta';
    document.getElementById('formVenta').action = '{{ route("operativo.ventas.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputCliente').value       = '';
    document.getElementById('inputFechaVenta').value    = '{{ now()->toDateString() }}';
    document.getElementById('inputObservaciones').value = '';
    limpiarItems();
    agregarItem(); // Una fila vacía por defecto
    abrirModal('modalForm');
}

function abrirModalEditar(venta) {
    document.getElementById('modalFormTitle').textContent = 'Editar Venta';
    document.getElementById('btnGuardar').textContent     = 'Guardar cambios';
    document.getElementById('formVenta').action = '/operativo/ventas/' + venta.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputCliente').value       = venta.cliente || '';
    document.getElementById('inputFechaVenta').value    = venta.fecha_venta ? venta.fecha_venta.substring(0,10) : (venta.fechaVenta ? venta.fechaVenta.substring(0,10) : '');
    document.getElementById('inputObservaciones').value = venta.observaciones || '';
    limpiarItems();
    if (venta.items && venta.items.length > 0) {
        venta.items.forEach(item => {
            const pct = (item.porcentaje_iva !== undefined && item.porcentaje_iva !== null) ? item.porcentaje_iva : 0;
            agregarItem(item.idProducto, item.cantidad, item.precioUnitario, pct);
        });
    } else {
        agregarItem();
    }
    abrirModal('modalForm');
}

function abrirModalEliminar(id) {
    document.getElementById('formEliminar').action = '/operativo/ventas/' + id;
    abrirModal('modalEliminar');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-operativo-layout>
