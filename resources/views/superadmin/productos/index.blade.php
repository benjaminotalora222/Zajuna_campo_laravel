@php $title = 'Productos'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Catálogo de Productos</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Administra los productos disponibles en la plataforma.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nuevo Producto
        </button>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

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
        <form method="GET" action="{{ route('superadmin.productos.index') }}"
              class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar producto</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nombre, categoría o código..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Categoría</label>
                <select name="categoria" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat }}" {{ request('categoria') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Stock</label>
                <select name="stock" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    <option value="bajo"    {{ request('stock') === 'bajo'    ? 'selected' : '' }}>Stock bajo</option>
                    <option value="agotado" {{ request('stock') === 'agotado' ? 'selected' : '' }}>Agotado</option>
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                    style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','categoria','stock','activo']))
            <a href="{{ route('superadmin.productos.index') }}"
               class="px-5 py-2.5 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
               style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- GRID DE PRODUCTOS --}}
    @if($productos->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6">
        @foreach($productos as $p)
        @php
            $catColors = [
                'Granos'       => ['bg'=>'#fef9c3','color'=>'#854d0e'],
                'Frutas'       => ['bg'=>'#fce7f3','color'=>'#9d174d'],
                'Verduras'     => ['bg'=>'#dcfce7','color'=>'#166534'],
                'Insumos'      => ['bg'=>'#ede9fe','color'=>'#6d28d9'],
                'Lácteos'      => ['bg'=>'#dbeafe','color'=>'#1e40af'],
                'Carnes'       => ['bg'=>'#fee2e2','color'=>'#991b1b'],
                'Tubérculos'   => ['bg'=>'#ffedd5','color'=>'#9a3412'],
                'Cereales'     => ['bg'=>'#fef3c7','color'=>'#92400e'],
            ];
            $cc = $catColors[$p->categoria] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];

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

                {{-- Badge stock estado --}}
                <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded-full text-white"
                      style="background:{{ $stockColor }};">
                    {{ $stockLabel }}
                </span>

                {{-- Botones acción --}}
                <div class="absolute top-2 right-2 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick='abrirModalEditar(@json($p))'
                            class="w-7 h-7 rounded-lg flex items-center justify-center shadow-md"
                            style="background:white; color:#39a900;" title="Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                        </svg>
                    </button>
                    <button onclick="abrirModalEliminar({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center shadow-md"
                            style="background:white; color:#ef4444;" title="Eliminar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-4 flex flex-col flex-1">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="font-bold text-sm leading-tight" style="color:#1c2b16;">{{ $p->nombre }}</h3>
                </div>

                @if($p->descripcion)
                <p class="text-xs leading-relaxed mb-3 line-clamp-2" style="color:#5a5a4f;">{{ $p->descripcion }}</p>
                @endif

                <div class="mt-auto">
                    {{-- Categoría badge --}}
                    @if($p->categoria)
                    <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-bold mb-2"
                          style="background:{{ $cc['bg'] }}; color:{{ $cc['color'] }};">
                        {{ $p->categoria }}
                    </span>
                    @endif

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-extrabold" style="color:#fdc300;">
                                ${{ number_format($p->precio ?? 0, 0, ',', '.') }}
                            </span>
                            <span class="text-xs ml-1" style="color:#9a9a8a;">/ {{ $p->unidad ?? 'un' }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold" style="color:{{ $stockColor }};">
                                {{ $p->stockActual ?? 0 }} {{ $p->unidad ?? '' }}
                            </p>
                            <p class="text-[10px]" style="color:#9a9a8a;">en stock</p>
                        </div>
                    </div>

                    {{-- Barra de stock --}}
                    @if($p->stockMinimo && $p->stockActual !== null)
                    <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                        @php
                            $maxRef = max($p->stockMinimo * 3, 1);
                            $pct    = min(100, round(($p->stockActual / $maxRef) * 100));
                        @endphp
                        <div class="h-full rounded-full transition-all"
                             style="width:{{ $pct }}%; background:{{ $stockColor }};"></div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer botones visibles --}}
            <div class="px-4 pb-4 flex gap-2">
                <button onclick='abrirModalEditar(@json($p))'
                        class="flex-1 py-2 rounded-xl text-xs font-bold transition hover:opacity-90"
                        style="background:#fdc300; color:#71277a;">
                    Ver / Editar
                </button>
                <button onclick="abrirModalEliminar({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                        class="w-9 py-2 rounded-xl flex items-center justify-center transition hover:opacity-90"
                        style="background:#fef2f2; color:#ef4444;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-20 text-center" style="color:#9a9a8a;">
        <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
        </svg>
        <p class="text-sm font-semibold">No se encontraron productos</p>
        <p class="text-xs mt-1">Intenta con otros filtros o agrega un nuevo producto.</p>
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
                <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </span>
            @else
                <a href="{{ $productos->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
            @endif
            @foreach($productos->getUrlRange(max(1,$productos->currentPage()-2), min($productos->lastPage(),$productos->currentPage()+2)) as $page => $url)
                @if($page == $productos->currentPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                @endif
            @endforeach
            @if($productos->hasMorePages())
                <a href="{{ $productos->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            @else
                <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                </span>
            @endif
        </div>
    </div>
    @else
    <p class="mt-2 text-xs" style="color:#9a9a8a;">Mostrando {{ $productos->count() }} producto{{ $productos->count() != 1 ? 's' : '' }}</p>
    @endif

</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
                    </svg>
                </div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Producto</h2>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formProducto" method="POST" enctype="multipart/form-data" class="px-7 py-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- Errores de validación --}}
            @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <p class="font-bold mb-1">Corrige los siguientes errores:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Nombre --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Nombre <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nombre" id="inputNombre" placeholder="Ej. Café Especial" required
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Descripción --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                    <textarea name="descripcion" id="inputDescripcion" rows="2" placeholder="Descripción breve del producto..."
                              class="w-full px-4 py-3 rounded-xl border text-sm outline-none resize-none"
                              style="border-color:#e7e0cc; background:#fafafa;"
                              onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                              onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'"></textarea>
                </div>

                {{-- Precio --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Precio <span style="color:#ef4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold" style="color:#9a9a8a;">$</span>
                        <input type="number" name="precio" id="inputPrecio" placeholder="0" step="0.01" min="0" required
                               class="w-full pl-7 pr-4 py-3 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    </div>
                </div>

                {{-- Unidad --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Unidad <span style="color:#ef4444;">*</span></label>
                    <select name="unidad" id="inputUnidad" required
                            class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="kg">kg</option>
                        <option value="g">g</option>
                        <option value="lb">lb</option>
                        <option value="litro">litro</option>
                        <option value="saco">saco</option>
                        <option value="caja">caja</option>
                        <option value="unidad">unidad</option>
                        <option value="bulto">bulto</option>
                        <option value="canasta">canasta</option>
                    </select>
                </div>

                {{-- Stock actual --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Stock actual <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="stockActual" id="inputStockActual" placeholder="0" min="0" required
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Stock mínimo --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Stock mínimo</label>
                    <input type="number" name="stockMinimo" id="inputStockMinimo" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Categoría --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Categoría <span style="color:#ef4444;">*</span></label>
                    <select id="selectCategoria"
                            class="w-full px-4 py-3 rounded-xl border text-sm outline-none mb-2"
                            style="border-color:#e7e0cc; background:#fafafa;"
                            onchange="onCatChange(this)">
                        <option value="">Selecciona...</option>
                        <option value="Granos">Granos</option>
                        <option value="Frutas">Frutas</option>
                        <option value="Verduras">Verduras</option>
                        <option value="Tubérculos">Tubérculos</option>
                        <option value="Lácteos">Lácteos</option>
                        <option value="Carnes">Carnes</option>
                        <option value="Cereales">Cereales</option>
                        <option value="Insumos">Insumos</option>
                        @foreach($categorias as $cat)
                            @if(!in_array($cat, ['Granos','Frutas','Verduras','Tubérculos','Lácteos','Carnes','Cereales','Insumos']))
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endif
                        @endforeach
                        <option value="__otro__">Otra...</option>
                    </select>
                    {{-- Este input SIEMPRE está presente y tiene el valor real --}}
                    <input type="text" name="categoria" id="inputCategoria"
                           placeholder="Escribe o selecciona una categoría" required
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Código de barras --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Código de barras</label>
                    <input type="text" name="codigoBarras" id="inputCodigoBarras" placeholder="Ej. 7702001..."
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Días perecedero --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Días perecedero máx.</label>
                    <input type="number" name="diasPerecederoMax" id="inputDiasPerecedero" placeholder="Ej. 30" min="0"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>

                {{-- Imagen --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Imagen del producto</label>
                    <div id="imagenPreviewWrap" class="hidden mb-2">
                        <img id="imagenPreview" src="" alt="Vista previa" class="h-24 rounded-xl object-cover border" style="border-color:#e7e0cc;">
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-dashed transition hover:border-green-500"
                           style="border-color:#e7e0cc; background:#fafafa;">
                        <svg class="w-5 h-5 shrink-0" style="color:#9a9a8a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span class="text-sm" style="color:#9a9a8a;" id="imagenLabel">Selecciona una imagen (JPG, PNG — máx. 2MB)</span>
                        <input type="file" name="imagen" id="inputImagen" accept="image/*" class="hidden"
                               onchange="previewImagen(this)">
                    </label>
                </div>

                {{-- Activo --}}
                <div class="col-span-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" id="inputActivo" value="1" checked
                               class="w-4 h-4 rounded cursor-pointer" style="accent-color:#39a900;">
                        <span class="text-sm font-medium" style="color:#5a5a4f;">Producto activo (visible en catálogo)</span>
                    </label>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-4 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                        style="background:#39a900;">
                    Crear producto
                </button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ELIMINAR ══════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar producto?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">
                Vas a eliminar <strong id="eliminarNombre"></strong>. Esta acción no se puede deshacer.
            </p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white" style="background:#ef4444;">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="cerrarModal('modalEliminar')"
                            class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                            style="border-color:#e7e0cc; color:#5a5a4f;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden'); m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}
['modalForm','modalEliminar'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});

function previewImagen(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagenPreview').src = e.target.result;
            document.getElementById('imagenPreviewWrap').classList.remove('hidden');
            document.getElementById('imagenLabel').textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resetForm() {
    document.getElementById('inputImagen').value = '';
    document.getElementById('imagenPreviewWrap').classList.add('hidden');
    document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG — máx. 2MB)';
    document.getElementById('selectCategoria').value = '';
    document.getElementById('inputCategoria').value = '';
}

function onCatChange(sel) {
    const inputCat = document.getElementById('inputCategoria');
    if (sel.value && sel.value !== '__otro__') {
        inputCat.value = sel.value;
    } else if (sel.value === '__otro__') {
        inputCat.value = '';
        inputCat.focus();
    }
}

function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nuevo Producto';
    document.getElementById('btnGuardar').textContent = 'Crear producto';
    document.getElementById('formProducto').action = '{{ route("superadmin.productos.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputDescripcion').value = '';
    document.getElementById('inputPrecio').value = '';
    document.getElementById('inputUnidad').value = 'kg';
    document.getElementById('inputStockActual').value = '';
    document.getElementById('inputStockMinimo').value = '';
    document.getElementById('inputCodigoBarras').value = '';
    document.getElementById('inputDiasPerecedero').value = '';
    document.getElementById('inputActivo').checked = true;
    resetForm();
    abrirModal('modalForm');
}

function abrirModalEditar(p) {
    document.getElementById('modalFormTitle').textContent = 'Editar Producto';
    document.getElementById('btnGuardar').textContent = 'Guardar cambios';
    document.getElementById('formProducto').action = '/superadmin/productos/' + p.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputNombre').value         = p.nombre || '';
    document.getElementById('inputDescripcion').value    = p.descripcion || '';
    document.getElementById('inputPrecio').value         = p.precio || '';
    document.getElementById('inputUnidad').value         = p.unidad || 'kg';
    document.getElementById('inputStockActual').value    = p.stockActual !== null ? p.stockActual : '';
    document.getElementById('inputStockMinimo').value    = p.stockMinimo !== null ? p.stockMinimo : '';
    document.getElementById('inputCodigoBarras').value   = p.codigoBarras || '';
    document.getElementById('inputDiasPerecedero').value = p.diasPerecederoMax !== null ? p.diasPerecederoMax : '';
    document.getElementById('inputActivo').checked       = p.activo == 1 || p.activo === true;
    document.getElementById('inputCategoria').value      = p.categoria || '';

    // Sincronizar select con la categoría
    const sel = document.getElementById('selectCategoria');
    let found = false;
    for (let opt of sel.options) {
        if (opt.value === p.categoria) { sel.value = p.categoria; found = true; break; }
    }
    if (!found) sel.value = p.categoria ? '__otro__' : '';

    // Imagen existente
    if (p.imagen) {
        document.getElementById('imagenPreview').src = '/storage/' + p.imagen;
        document.getElementById('imagenPreviewWrap').classList.remove('hidden');
        document.getElementById('imagenLabel').textContent = 'Imagen actual (sube otra para reemplazarla)';
    } else {
        document.getElementById('imagenPreviewWrap').classList.add('hidden');
        document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG — máx. 2MB)';
    }
    document.getElementById('inputImagen').value = '';
    abrirModal('modalForm');
}

function abrirModalEliminar(id, nombre) {
    document.getElementById('eliminarNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '/superadmin/productos/' + id;
    abrirModal('modalEliminar');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-superadmin-layout>
