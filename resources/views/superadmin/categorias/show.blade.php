@php $title = $categoria->nombre; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('superadmin.categorias.index') }}"
               class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-white border transition shrink-0"
               style="border-color:#e7e0cc; color:#5a5a4f;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
            </a>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0" style="background:#faf5ff;">
                {{ $categoria->icono ?? '📦' }}
            </div>
            <div>
                <h1 class="text-2xl font-extrabold leading-tight">{{ $categoria->nombre }}</h1>
                <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                    {{ $categoria->descripcion ?: 'Productos de esta categoría.' }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if($categoria->activo)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold" style="background:#f0fdf4; color:#166534;">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold" style="background:#fffbeb; color:#d97706;">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Inactivo
                </span>
            @endif
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#71277a;">Total</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $stats['total'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Productos</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#39a900;">Activos</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $stats['activos'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Disponibles</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#d97706;">Stock bajo</p>
            <p class="text-2xl font-extrabold" style="color:{{ $stats['bajo'] > 0 ? '#d97706' : '#1c2b16' }};">{{ $stats['bajo'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Por reponer</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color:#ef4444;">Agotados</p>
            <p class="text-2xl font-extrabold" style="color:{{ $stats['agotado'] > 0 ? '#ef4444' : '#1c2b16' }};">{{ $stats['agotado'] }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Sin stock</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('superadmin.categorias.show', $categoria) }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar producto..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[140px]">
                <select name="stock" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todo el stock</option>
                    <option value="bajo"    {{ request('stock') === 'bajo'    ? 'selected' : '' }}>Stock bajo</option>
                    <option value="agotado" {{ request('stock') === 'agotado' ? 'selected' : '' }}>Agotado</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','stock']))
                <a href="{{ route('superadmin.categorias.show', $categoria) }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- GRID DE PRODUCTOS --}}
    @if($productos->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
        @foreach($productos as $p)
        @php
            $stockColor = match($p->stock_estado) {
                'agotado' => '#ef4444',
                'bajo'    => '#d97706',
                default   => '#39a900',
            };
            $stockLabel = match($p->stock_estado) {
                'agotado'   => 'Agotado',
                'bajo'      => 'Stock bajo',
                'sin_datos' => '—',
                default     => 'Disponible',
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group">

            {{-- Imagen --}}
            <div class="relative h-44 overflow-hidden" style="background:#f3f0e8;">
                @if($p->imagen)
                    <img src="{{ Storage::url($p->imagen) }}" alt="{{ $p->nombre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="m21 15-5-5L5 21"/>
                        </svg>
                    </div>
                @endif
                <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded-full text-white"
                      style="background:{{ $stockColor }};">{{ $stockLabel }}</span>
            </div>

            {{-- Info --}}
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-sm leading-tight mb-1" style="color:#1c2b16;">{{ $p->nombre }}</h3>
                @if($p->descripcion)
                    <p class="text-xs leading-relaxed mb-3 line-clamp-2" style="color:#5a5a4f;">{{ $p->descripcion }}</p>
                @endif
                <div class="mt-auto flex items-center justify-between">
                    <div>
                        <span class="text-lg font-extrabold" style="color:#fdc300;">
                            ${{ number_format($p->precio ?? 0, 0, ',', '.') }}
                        </span>
                        <span class="text-xs ml-1" style="color:#9a9a8a;">/ {{ $p->unidad ?? 'un' }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold" style="color:{{ $stockColor }};">{{ $p->stockActual ?? 0 }} {{ $p->unidad ?? '' }}</p>
                        <p class="text-[10px]" style="color:#9a9a8a;">en stock</p>
                    </div>
                </div>
                @if($p->stockMinimo && $p->stockActual !== null)
                <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    @php
                        $maxRef = max($p->stockMinimo * 3, 1);
                        $pct    = min(100, round(($p->stockActual / $maxRef) * 100));
                    @endphp
                    <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $stockColor }};"></div>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-4 pb-4">
                <a href="{{ route('superadmin.productos.index', ['search' => $p->nombre]) }}"
                   class="block w-full py-2 rounded-xl text-xs font-bold text-center transition hover:opacity-90"
                   style="background:#fdc300; color:#71277a;">
                    Ver en productos
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-20 text-center" style="color:#9a9a8a;">
        <div class="text-4xl mb-4">{{ $categoria->icono ?? '📦' }}</div>
        <p class="text-sm font-semibold">No hay productos en "{{ $categoria->nombre }}"</p>
        <p class="text-xs mt-1">Agrega productos desde el módulo de productos y asígnales esta categoría.</p>
        <a href="{{ route('superadmin.productos.index') }}"
           class="inline-block mt-4 px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90"
           style="background:#39a900;">Ir a productos</a>
    </div>
    @endif

    {{-- PAGINACIÓN --}}
    @if($productos->hasPages())
    <div class="flex items-center justify-between mt-2">
        <p class="text-xs" style="color:#9a9a8a;">
            Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} productos
        </p>
        <div class="flex items-center gap-1">
            @if($productos->onFirstPage())
                <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></span>
            @else
                <a href="{{ $productos->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></a>
            @endif
            @foreach($productos->getUrlRange(max(1,$productos->currentPage()-2), min($productos->lastPage(),$productos->currentPage()+2)) as $page => $url)
                @if($page == $productos->currentPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                @endif
            @endforeach
            @if($productos->hasMorePages())
                <a href="{{ $productos->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
            @else
                <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></span>
            @endif
        </div>
    </div>
    @endif

</div>
</x-superadmin-layout>
