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
                                {{ number_format($p->stock_calculado, 0, ',', '.') }} {{ $p->unidad ?? '' }}
                            </p>
                            <p class="text-[10px]" style="color:#9a9a8a;">en stock</p>
                        </div>
                    </div>

                    {{-- Barra de stock --}}
                    @if($p->stockMinimo && $p->stock_calculado !== null)
                    <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                        @php
                            $maxRef = max($p->stockMinimo * 3, 1);
                            $pct    = min(100, round(($p->stock_calculado / $maxRef) * 100));
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

        <form id="formProducto" method="POST" action="{{ route('superadmin.productos.store') }}" enctype="multipart/form-data" class="px-7 py-6">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST" disabled>

            {{-- Banner informativo en modo edición --}}
            <div id="bannerModoEdicion" class="hidden mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Estás editando: <strong id="bannerEditNombre"></strong> (modificará este registro existente).</span>
                </div>
                <button type="button" onclick="abrirModalCrear()" class="text-xs font-bold text-purple-700 underline hover:text-purple-900 shrink-0 ml-2">
                    ¿Crear nuevo en su lugar?
                </button>
            </div>

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
                    <input type="text" name="nombre" id="inputNombre" placeholder="Ej. Café Especial" required maxlength="255"
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ][a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\.\'\(\)]*$"
                           title="El nombre del producto debe comenzar con una letra y solo contener letras (no se permiten números)"
                           oninput="if(/^[0-9]/.test(this.value)){ this.setCustomValidity('No puede comenzar con números'); this.reportValidity(); } else { this.setCustomValidity(''); } this.value = this.value.replace(/^[0-9]+/, '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\.\'\(\)]/g, '')"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo letras y espacios (no se permiten números).</p>
                </div>

                {{-- Descripción --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                    <textarea name="descripcion" id="inputDescripcion" rows="2" maxlength="1000" placeholder="Descripción breve del producto..."
                              class="w-full px-4 py-3 rounded-xl border text-sm outline-none resize-none"
                              style="border-color:#e7e0cc; background:#fafafa;"
                              onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                              onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'"></textarea>
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Texto descriptivo opcional (máx. 1000 caracteres).</p>
                </div>

                {{-- Precio --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Precio <span style="color:#ef4444;">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold" style="color:#9a9a8a;">$</span>
                        <input type="number" name="precio" id="inputPrecio" placeholder="0" step="0.01" min="0" max="99999999.99" required
                               onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();"
                               oninput="if(parseFloat(this.value) > 99999999.99) this.value = '99999999.99';"
                               class="w-full pl-7 pr-4 py-3 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    </div>
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo números y decimales (máx. $99.999.999,99).</p>
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
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Selecciona la unidad de medida.</p>
                </div>

                {{-- Tarifa IVA Sugerida --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">IVA sugerido (%)</label>
                    <select name="iva" id="inputIva"
                            class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="0">0% (Sin IVA / Exento)</option>
                        <option value="5">5%</option>
                        <option value="19">19% (Tarifa General)</option>
                    </select>
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Tarifa por defecto en el formulario de venta.</p>
                </div>

                {{-- Stock mínimo (alertas) — el stock real viene de lotes/movimientos --}}
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Stock mínimo de alerta</label>
                    <input type="number" name="stockMinimo" id="inputStockMinimo" placeholder="0" min="0" max="9999999" step="1"
                           onkeydown="if(['e','E','+','-','.','-','.'].includes(event.key)) event.preventDefault();"
                           oninput="this.value = this.value.replace(/\D/g, ''); if(parseInt(this.value) > 9999999) this.value = '9999999';"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo números enteros (unidades de alerta, máx. 9.999.999).</p>
                </div>

                {{-- Categoría con Autocomplete --}}
                <div class="col-span-2 sm:col-span-1 relative">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold" style="color:#1c2b16;">Categoría <span style="color:#ef4444;">*</span></label>
                        <button type="button" onclick="abrirModalRapidoCategoria()"
                                class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-0.5 rounded-lg border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100 transition shadow-2xs"
                                title="Crear una nueva categoría sin salir de este formulario">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                            <span>+ Nueva categoría</span>
                        </button>
                    </div>
                    <div class="relative">
                        <input type="text" name="categoria" id="inputCategoria"
                               placeholder="Escribe la categoría..." required maxlength="100"
                               autocomplete="off"
                               pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ][a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]*$"
                               title="La categoría debe comenzar con una letra y solo contener letras (no se permiten números)"
                               oninput="if(/^[0-9]/.test(this.value)){ this.setCustomValidity('No puede comenzar con números'); this.reportValidity(); } else { this.setCustomValidity(''); } this.value = this.value.replace(/^[0-9]+/, '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]/g, '');"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                        
                        {{-- Icono buscador / sugerencias --}}
                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>

                        {{-- Menú de Autocomplete / Sugerencias desplegable --}}
                        <div id="categoriaAutocompleteList"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-xl border shadow-xl z-50 max-h-48 overflow-y-auto hidden"
                             style="border-color:#e7e0cc;">
                        </div>
                    </div>
                    
                    {{-- Sugerencias rápidas en chips --}}
                    <div class="flex items-center gap-1.5 flex-wrap mt-2" id="sugerenciasCategoriasChips">
                        <span class="text-[11px] text-gray-400 font-semibold">Sugerencias:</span>
                        @foreach($categorias->take(4) as $sugCat)
                            <button type="button" onclick="seleccionarCategoria('{{ $sugCat }}')"
                                    class="text-[11px] font-bold px-2 py-0.5 rounded-lg border bg-white hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700 transition"
                                    style="border-color:#e7e0cc; color:#5a5a4f;">
                                {{ $sugCat }}
                            </button>
                        @endforeach
                    </div>
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo letras (sin números). Escribe para autocompletar.</p>
                </div>

                {{-- Proveedor con Autocomplete --}}
                <div class="col-span-2 relative">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Proveedor <span style="color:#ef4444;">*</span></label>
                    <div class="relative">
                        <input type="text" id="inputProveedorNombre"
                               placeholder="Escribe el nombre del proveedor..." required maxlength="255"
                               autocomplete="off"
                               class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                        
                        <input type="hidden" name="proveedor_id" id="inputProveedorId" required value="{{ old('proveedor_id') }}">

                        {{-- Icono buscador / sugerencias --}}
                        <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>

                        {{-- Menú de Autocomplete / Sugerencias desplegable --}}
                        <div id="proveedorAutocompleteList"
                             class="absolute left-0 right-0 top-full mt-1.5 bg-white rounded-xl border shadow-xl z-50 max-h-48 overflow-y-auto hidden"
                             style="border-color:#e7e0cc;">
                        </div>
                    </div>

                    {{-- Sugerencias rápidas en chips --}}
                    @if($proveedores->count() > 0)
                    <div class="flex items-center gap-1.5 flex-wrap mt-2">
                        <span class="text-[11px] text-gray-400 font-semibold">Sugerencias:</span>
                        @foreach($proveedores->take(4) as $prvChip)
                            <button type="button" onclick="seleccionarProveedor({{ $prvChip->id }}, '{{ addslashes($prvChip->nombre) }}')"
                                    class="text-[11px] font-bold px-2 py-0.5 rounded-lg border bg-white hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-700 transition"
                                    style="border-color:#e7e0cc; color:#5a5a4f;">
                                {{ $prvChip->nombre }}
                            </button>
                        @endforeach
                    </div>
                    @endif
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Escribe para buscar y autocompletar el productor o proveedor asociado.</p>
                </div>

                {{-- Código de barras --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Código de barras</label>
                    <input type="text" name="codigoBarras" id="inputCodigoBarras" placeholder="Ej. 7702001..." maxlength="50"
                           pattern="^[0-9]*$"
                           title="Solo números"
                           oninput="this.value = this.value.replace(/\D/g, '')"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo números (sin letras ni símbolos).</p>
                </div>

                {{-- Días perecedero --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Días perecedero máx.</label>
                    <input type="number" name="diasPerecederoMax" id="inputDiasPerecedero" placeholder="Ej. 30" min="0" max="3650" step="1"
                           onkeydown="if(['e','E','+','-','.','-','.'].includes(event.key)) event.preventDefault();"
                           oninput="this.value = this.value.replace(/\D/g, ''); if(parseInt(this.value) > 3650) this.value = '3650';"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Solo números enteros (días, máx. 3.650).</p>
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
                        <span class="text-sm" style="color:#9a9a8a;" id="imagenLabel">Selecciona una imagen (JPG, PNG, WEBP — máx. 2MB)</span>
                        <input type="file" name="imagen" id="inputImagen" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden"
                               onchange="previewImagen(this)">
                    </label>
                    <p class="text-[11px] mt-1" style="color:#9a9a8a;">Formatos permitidos: JPG, PNG, WEBP (máx. 2MB).</p>
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

{{-- ══ MODAL RÁPIDO CREAR CATEGORÍA ══════════════════════════════ --}}
<div id="modalRapidoCategoria" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.65);" onclick="cerrarModalRapidoCategoria()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gradient-to-r from-purple-50/60 to-white" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-purple-100 text-purple-700 shadow-xs">
                    <span class="text-lg">🏷️</span>
                </div>
                <div>
                    <h3 class="font-extrabold text-base" style="color:#1c2b16;">Nueva Categoría</h3>
                    <p class="text-xs text-gray-500">Se agregará al catálogo y se seleccionará de inmediato.</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalRapidoCategoria()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">
            <div id="quickCatErrorWrap" class="hidden p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-600 font-semibold"></div>

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#1c2b16;">
                    Nombre de la Categoría <span class="text-red-500">*</span>
                </label>
                <input type="text" id="quickCatNombre" placeholder="Ej. Lácteos, Granos, Frutas..." required maxlength="100"
                       pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ][a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]*$"
                       title="Solo letras y espacios (no se permiten números)"
                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); guardarCategoriaRapida(); }"
                       oninput="if(/^[0-9]/.test(this.value)){ this.setCustomValidity('No puede comenzar con números'); this.reportValidity(); } else { this.setCustomValidity(''); } this.value = this.value.replace(/^[0-9]+/, '').replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]/g, '');"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                       style="border-color:#e7e0cc; background:#fafafa;">
                <p class="text-[11px] mt-1 text-gray-400">Solo letras y espacios (mín. 3 letras, sin números).</p>
            </div>

            {{-- Icono rápido --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#1c2b16;">
                    Ícono / Emoji
                </label>
                <div class="flex items-center gap-2">
                    <input type="text" id="quickCatIcono" value="📦" maxlength="10"
                           class="w-14 px-2 py-2 rounded-xl border text-center text-xl outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <div class="flex items-center gap-1.5 flex-wrap flex-1">
                        @foreach(['📦','🍎','🥦','🥛','🥩','🌾','🧴','☕'] as $emo)
                            <button type="button" onclick="document.getElementById('quickCatIcono').value = '{{ $emo }}'"
                                    class="w-8 h-8 rounded-lg border text-sm hover:bg-gray-100 transition flex items-center justify-center bg-white"
                                    style="border-color:#e7e0cc;">
                                {{ $emo }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Descripción opcional --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#1c2b16;">
                    Descripción <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea id="quickCatDescripcion" rows="2" maxlength="500" placeholder="Breve descripción de la categoría..."
                          class="w-full px-4 py-2 rounded-xl border text-sm outline-none resize-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t bg-gray-50/60" style="border-color:#f3f0e8;">
            <button type="button" onclick="cerrarModalRapidoCategoria()"
                    class="px-4 py-2 rounded-xl border text-xs font-bold text-gray-600 hover:bg-white transition"
                    style="border-color:#e7e0cc;">
                Cancelar
            </button>
            <button type="button" id="btnGuardarQuickCat" onclick="guardarCategoriaRapida()"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90"
                    style="background:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
                <span>Guardar Categoría</span>
            </button>
        </div>
    </div>
</div>

<script>
// ══ CONTROL MODAL RÁPIDO CATEGORÍA ══════════════════════════════
function mostrarErrorQuickCat(msg) {
    const errorBox = document.getElementById('quickCatErrorWrap');
    if (errorBox) {
        errorBox.textContent = msg;
        errorBox.classList.remove('hidden');
    }
}

function limpiarErrorQuickCat() {
    const errorBox = document.getElementById('quickCatErrorWrap');
    if (errorBox) {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
    }
}

window.abrirModalRapidoCategoria = function() {
    limpiarErrorQuickCat();
    const modal = document.getElementById('modalRapidoCategoria');
    const inputCat = document.getElementById('inputCategoria');
    const quickNombre = document.getElementById('quickCatNombre');
    const quickIcono = document.getElementById('quickCatIcono');
    const quickDesc = document.getElementById('quickCatDescripcion');

    if (inputCat && inputCat.value.trim()) {
        quickNombre.value = inputCat.value.trim();
    } else {
        quickNombre.value = '';
    }
    quickIcono.value = '📦';
    quickDesc.value = '';

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        quickNombre.focus();
        quickNombre.select();
    }, 80);
};

window.cerrarModalRapidoCategoria = function() {
    limpiarErrorQuickCat();
    const modal = document.getElementById('modalRapidoCategoria');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    const inputCat = document.getElementById('inputCategoria');
    if (inputCat) inputCat.focus();
};

window.guardarCategoriaRapida = function() {
    limpiarErrorQuickCat();
    const quickNombre = document.getElementById('quickCatNombre');
    const quickIcono = document.getElementById('quickCatIcono');
    const quickDesc = document.getElementById('quickCatDescripcion');
    const btnGuardar = document.getElementById('btnGuardarQuickCat');

    const nombre = quickNombre.value.trim();
    const icono = quickIcono.value.trim() || '📦';
    const descripcion = quickDesc.value.trim();

    if (!nombre) {
        mostrarErrorQuickCat('El nombre de la categoría es obligatorio.');
        quickNombre.focus();
        return;
    }
    if (/^[0-9]/.test(nombre)) {
        mostrarErrorQuickCat('El nombre no puede comenzar con números.');
        quickNombre.focus();
        return;
    }
    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ][a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]*$/.test(nombre)) {
        mostrarErrorQuickCat('Solo se permiten letras y espacios (sin números ni símbolos especiales).');
        quickNombre.focus();
        return;
    }
    if (nombre.length < 3) {
        mostrarErrorQuickCat('El nombre debe tener al menos 3 caracteres.');
        quickNombre.focus();
        return;
    }

    // Verificar si ya existe en las categorías cargadas
    const yaExiste = categoriasDisponibles.some(c => c.toLowerCase() === nombre.toLowerCase());
    if (yaExiste) {
        mostrarErrorQuickCat('Ya existe una categoría registrada con este nombre.');
        quickNombre.focus();
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;

    const originalHtml = btnGuardar.innerHTML;
    btnGuardar.disabled = true;
    btnGuardar.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg> Guardando...`;

    fetch('{{ route("superadmin.categorias.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            nombre: nombre,
            icono: icono,
            descripcion: descripcion,
            activo: true
        })
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            let errorMsg = data.message || 'Error al guardar la categoría.';
            if (data.errors) {
                const firstErr = Object.values(data.errors)[0];
                if (Array.isArray(firstErr)) errorMsg = firstErr[0];
            }
            throw new Error(errorMsg);
        }
        return data;
    })
    .then(data => {
        const catNombre = (data.categoria && data.categoria.nombre) ? data.categoria.nombre : nombre;

        // Añadir a las disponibles
        if (!categoriasDisponibles.includes(catNombre)) {
            categoriasDisponibles.unshift(catNombre);
        }

        // Establecer en el input del producto
        const inputCat = document.getElementById('inputCategoria');
        if (inputCat) {
            inputCat.value = catNombre;
            inputCat.setCustomValidity('');
            inputCat.classList.remove('border-red-500');
        }

        // Añadir chip visual si existe contenedor
        const chipsContainer = document.getElementById('sugerenciasCategoriasChips');
        if (chipsContainer) {
            const chipBtn = document.createElement('button');
            chipBtn.type = 'button';
            chipBtn.className = 'text-[11px] font-bold px-2 py-0.5 rounded-lg border bg-purple-50 text-purple-700 border-purple-200 transition hover:bg-purple-100';
            chipBtn.textContent = catNombre;
            chipBtn.onclick = () => window.seleccionarCategoria(catNombre);
            if (chipsContainer.children.length > 1) {
                chipsContainer.insertBefore(chipBtn, chipsContainer.children[1]);
            } else {
                chipsContainer.appendChild(chipBtn);
            }
        }

        cerrarModalRapidoCategoria();

        // Notificación de éxito
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: '¡Categoría creada!',
                text: `"${catNombre}" se guardó y se seleccionó de inmediato.`,
                timer: 2200,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(err => {
        mostrarErrorQuickCat(err.message || 'Error de conexión al registrar la categoría.');
    })
    .finally(() => {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = originalHtml;
    });
};

function abrirModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden'); m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    if (id === 'modalForm') {
        const form = document.getElementById('formProducto');
        if (form) form.action = '{{ route("superadmin.productos.store") }}';
        const methodInput = document.getElementById('formMethod');
        if (methodInput) {
            methodInput.value = 'POST';
            methodInput.disabled = true;
        }
        const bannerEdit = document.getElementById('bannerModoEdicion');
        if (bannerEdit) bannerEdit.classList.add('hidden');
    }
    document.body.style.overflow = '';
}
['modalForm','modalEliminar'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});

// Validación en cliente y control de submit
function mostrarErrorModal(msg) {
    let errBox = document.getElementById('clienteErrorBox');
    if (!errBox) {
        errBox = document.createElement('div');
        errBox.id = 'clienteErrorBox';
        errBox.className = 'mb-4 px-4 py-3 rounded-xl text-sm';
        errBox.style.background = '#fef2f2';
        errBox.style.border = '1px solid #fca5a5';
        errBox.style.color = '#991b1b';
        const form = document.getElementById('formProducto');
        form.insertBefore(errBox, form.querySelector('.grid'));
    }
    errBox.innerHTML = '<p class="font-bold mb-1">Por favor verifica los campos:</p><ul class="list-disc list-inside space-y-0.5 text-xs"><li>' + msg + '</li></ul>';
    errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function limpiarErrorModal() {
    const errBox = document.getElementById('clienteErrorBox');
    if (errBox) errBox.remove();
}

document.getElementById('formProducto').addEventListener('submit', function (e) {
    limpiarErrorModal();

    const nombre = document.getElementById('inputNombre').value.trim();
    const categoria = document.getElementById('inputCategoria').value.trim();
    const precio = parseFloat(document.getElementById('inputPrecio').value);
    const codigoBarras = document.getElementById('inputCodigoBarras').value.trim();
    const stockMin = document.getElementById('inputStockMinimo').value.trim();
    const diasMax = document.getElementById('inputDiasPerecedero').value.trim();
    const proveedorId = document.getElementById('inputProveedorId').value;

    const regexTexto = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-\.\'\(\)]+$/;
    const regexCat = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]+$/;

    // Validación Nombre: solo letras y sin números
    if (!nombre) {
        e.preventDefault();
        mostrarErrorModal('El nombre del producto es obligatorio.');
        document.getElementById('inputNombre').focus();
        return false;
    }
    if (/[0-9]/.test(nombre) || !regexTexto.test(nombre)) {
        e.preventDefault();
        mostrarErrorModal('El nombre solo puede contener letras y espacios (no se permiten números).');
        document.getElementById('inputNombre').focus();
        return false;
    }
    if (nombre.length < 3) {
        e.preventDefault();
        mostrarErrorModal('El nombre debe tener al menos 3 caracteres.');
        document.getElementById('inputNombre').focus();
        return false;
    }

    // Validación Precio
    if (isNaN(precio) || precio < 0) {
        e.preventDefault();
        mostrarErrorModal('El precio debe ser un número válido igual o mayor a 0.');
        document.getElementById('inputPrecio').focus();
        return false;
    }
    if (precio > 99999999.99) {
        e.preventDefault();
        mostrarErrorModal('El precio no puede exceder $99.999.999,99.');
        document.getElementById('inputPrecio').focus();
        return false;
    }

    // Validación Categoría: solo letras y sin números
    if (!categoria) {
        e.preventDefault();
        mostrarErrorModal('La categoría es obligatoria.');
        document.getElementById('inputCategoria').focus();
        return false;
    }
    if (/[0-9]/.test(categoria) || !regexCat.test(categoria)) {
        e.preventDefault();
        mostrarErrorModal('La categoría solo puede contener letras y espacios (no se permiten números).');
        document.getElementById('inputCategoria').focus();
        return false;
    }

    // Validación Proveedor
    if (!proveedorId) {
        e.preventDefault();
        mostrarErrorModal('Debes seleccionar un proveedor de las sugerencias.');
        document.getElementById('inputProveedorNombre').focus();
        return false;
    }

    // Validación Código de barras: solo números
    if (codigoBarras && !/^[0-9]+$/.test(codigoBarras)) {
        e.preventDefault();
        mostrarErrorModal('El código de barras solo debe contener números.');
        document.getElementById('inputCodigoBarras').focus();
        return false;
    }

    // Validación Stock mínimo: entero
    if (stockMin !== '') {
        const smVal = parseInt(stockMin, 10);
        if (isNaN(smVal) || smVal < 0 || smVal > 9999999) {
            e.preventDefault();
            mostrarErrorModal('El stock mínimo debe ser un número entero entre 0 y 9.999.999.');
            document.getElementById('inputStockMinimo').focus();
            return false;
        }
    }

    // Validación Días perecedero: entero
    if (diasMax !== '') {
        const dpVal = parseInt(diasMax, 10);
        if (isNaN(dpVal) || dpVal < 0 || dpVal > 3650) {
            e.preventDefault();
            mostrarErrorModal('Los días perecederos deben ser un número entero entre 0 y 3.650 (10 años).');
            document.getElementById('inputDiasPerecedero').focus();
            return false;
        }
    }

    // Formulario válido: deshabilitar para prevenir doble click
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    btn.style.opacity = '0.6';
    btn.style.cursor = 'not-allowed';
});

function previewImagen(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            mostrarErrorModal('Solo se permiten imágenes en formato JPG, PNG o WEBP.');
            input.value = '';
            document.getElementById('imagenPreviewWrap').classList.add('hidden');
            document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG, WEBP — máx. 2MB)';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            mostrarErrorModal('La imagen no debe superar los 2MB de tamaño.');
            input.value = '';
            document.getElementById('imagenPreviewWrap').classList.add('hidden');
            document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG, WEBP — máx. 2MB)';
            return;
        }

        limpiarErrorModal();
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagenPreview').src = e.target.result;
            document.getElementById('imagenPreviewWrap').classList.remove('hidden');
            document.getElementById('imagenLabel').textContent = file.name;
        };
        reader.readAsDataURL(file);
    }
}

function resetForm() {
    limpiarErrorModal();
    document.getElementById('inputImagen').value = '';
    document.getElementById('imagenPreviewWrap').classList.add('hidden');
    document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG, WEBP — máx. 2MB)';
    document.getElementById('inputCategoria').value = '';
    document.getElementById('inputProveedorId').value = '';
    document.getElementById('inputProveedorNombre').value = '';
    const menuCat = document.getElementById('categoriaAutocompleteList');
    if (menuCat) menuCat.classList.add('hidden');
    const menuPrv = document.getElementById('proveedorAutocompleteList');
    if (menuPrv) menuPrv.classList.add('hidden');
}

function abrirModalCrear() {
    limpiarErrorModal();
    document.getElementById('modalFormTitle').textContent = 'Nuevo Producto';
    const bannerEdit = document.getElementById('bannerModoEdicion');
    if (bannerEdit) bannerEdit.classList.add('hidden');

    const btn = document.getElementById('btnGuardar');
    btn.textContent = 'Crear producto';
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';

    const form = document.getElementById('formProducto');
    form.action = '{{ route("superadmin.productos.store") }}';

    // Deshabilitar _method para garantizar petición POST nativa (store)
    const methodInput = document.getElementById('formMethod');
    if (methodInput) {
        methodInput.value = 'POST';
        methodInput.disabled = true;
    }

    document.getElementById('inputNombre').value = '';
    document.getElementById('inputDescripcion').value = '';
    document.getElementById('inputPrecio').value = '';
    document.getElementById('inputUnidad').value = 'kg';
    document.getElementById('inputStockMinimo').value = '';
    document.getElementById('inputCodigoBarras').value = '';
    document.getElementById('inputDiasPerecedero').value = '';
    document.getElementById('inputIva').value = '0';
    document.getElementById('inputActivo').checked = true;
    document.getElementById('inputProveedorId').value = '';
    document.getElementById('inputProveedorNombre').value = '';
    resetForm();
    abrirModal('modalForm');
}

function abrirModalEditar(p) {
    limpiarErrorModal();
    document.getElementById('modalFormTitle').textContent = 'Editar Producto';

    const bannerEdit = document.getElementById('bannerModoEdicion');
    if (bannerEdit) {
        bannerEdit.classList.remove('hidden');
        const bNom = document.getElementById('bannerEditNombre');
        if (bNom) bNom.textContent = p.nombre || 'Sin nombre';
    }

    const btn = document.getElementById('btnGuardar');
    btn.textContent = 'Guardar cambios';
    btn.disabled = false;
    btn.style.opacity = '1';
    btn.style.cursor = 'pointer';

    const form = document.getElementById('formProducto');
    form.action = '/superadmin/productos/' + p.id;

    // Habilitar _method como PUT para actualización
    const methodInput = document.getElementById('formMethod');
    if (methodInput) {
        methodInput.disabled = false;
        methodInput.value = 'PUT';
    }
    document.getElementById('inputNombre').value         = p.nombre || '';
    document.getElementById('inputDescripcion').value    = p.descripcion || '';
    document.getElementById('inputPrecio').value         = p.precio || '';
    document.getElementById('inputUnidad').value         = p.unidad || 'kg';
    document.getElementById('inputStockMinimo').value    = p.stockMinimo !== null ? p.stockMinimo : '';
    document.getElementById('inputCodigoBarras').value   = p.codigoBarras || '';
    document.getElementById('inputDiasPerecedero').value = p.diasPerecederoMax !== null ? p.diasPerecederoMax : '';
    const ivaVal = p.iva !== null && p.iva !== undefined ? Math.round(p.iva) : 0;
    document.getElementById('inputIva').value            = [0, 5, 19].includes(ivaVal) ? String(ivaVal) : '0';
    document.getElementById('inputActivo').checked       = p.activo == 1 || p.activo === true;
    document.getElementById('inputCategoria').value      = p.categoria || '';
    document.getElementById('inputProveedorId').value    = p.proveedor_id || '';
    const prv = proveedoresDisponibles.find(x => x.id == p.proveedor_id);
    document.getElementById('inputProveedorNombre').value = prv ? prv.nombre : '';

    // Imagen existente
    if (p.imagen) {
        document.getElementById('imagenPreview').src = '/storage/' + p.imagen;
        document.getElementById('imagenPreviewWrap').classList.remove('hidden');
        document.getElementById('imagenLabel').textContent = 'Imagen actual (sube otra para reemplazarla)';
    } else {
        document.getElementById('imagenPreviewWrap').classList.add('hidden');
        document.getElementById('imagenLabel').textContent = 'Selecciona una imagen (JPG, PNG, WEBP — máx. 2MB)';
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
    document.addEventListener('DOMContentLoaded', () => {
        abrirModalCrear();
        document.getElementById('inputNombre').value = @json(old('nombre', ''));
        document.getElementById('inputDescripcion').value = @json(old('descripcion', ''));
        document.getElementById('inputPrecio').value = @json(old('precio', ''));
        document.getElementById('inputUnidad').value = @json(old('unidad', 'kg'));
        document.getElementById('inputStockMinimo').value = @json(old('stockMinimo', ''));
        document.getElementById('inputCodigoBarras').value = @json(old('codigoBarras', ''));
        document.getElementById('inputDiasPerecedero').value = @json(old('diasPerecederoMax', ''));
        document.getElementById('inputIva').value = @json(old('iva', '0'));
        document.getElementById('inputCategoria').value = @json(old('categoria', ''));
        const oldPrvId = @json(old('proveedor_id', ''));
        document.getElementById('inputProveedorId').value = oldPrvId;
        const prvError = proveedoresDisponibles.find(x => x.id == oldPrvId);
        if (prvError) document.getElementById('inputProveedorNombre').value = prvError.nombre;
    });
@endif

// ── Autocomplete interactivo de categorías ──
let categoriasDisponibles = @json($categorias);

function initCategoriaAutocomplete() {
    const inputCat = document.getElementById('inputCategoria');
    const menuCat  = document.getElementById('categoriaAutocompleteList');
    if (!inputCat || !menuCat) return;

    let highlightedIndex = -1;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderSugerencias(filtro = '') {
        const query = filtro.trim().toLowerCase();
        const matches = query === ''
            ? categoriasDisponibles.slice(0, 8)
            : categoriasDisponibles.filter(c => c.toLowerCase().includes(query)).slice(0, 10);

        highlightedIndex = -1;

        if (matches.length === 0) {
            menuCat.innerHTML = `
                <div class="px-4 py-2.5 text-xs text-gray-500 italic bg-gray-50">
                    No hay sugerencias exactas. Puedes escribir y registrar una nueva categoría.
                </div>`;
            menuCat.classList.remove('hidden');
            return;
        }

        menuCat.innerHTML = matches.map((cat, idx) => `
            <div class="categoria-suggestion-item px-4 py-2.5 text-sm cursor-pointer transition flex items-center justify-between hover:bg-emerald-50 hover:text-emerald-800 ${idx === 0 ? 'bg-gray-50/70' : ''}"
                 data-val="${escapeHtml(cat)}"
                 onmousedown="seleccionarCategoria('${escapeHtml(cat)}')">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold text-gray-800">${escapeHtml(cat)}</span>
                </div>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Sugerencia</span>
            </div>
        `).join('');

        menuCat.classList.remove('hidden');
    }

    inputCat.addEventListener('focus', function() {
        renderSugerencias(this.value);
    });

    inputCat.addEventListener('input', function() {
        renderSugerencias(this.value);
    });

    inputCat.addEventListener('keydown', function(e) {
        if (menuCat.classList.contains('hidden')) return;

        const items = menuCat.querySelectorAll('.categoria-suggestion-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = (highlightedIndex + 1) % items.length;
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = (highlightedIndex - 1 + items.length) % items.length;
            updateHighlight(items);
        } else if (e.key === 'Enter') {
            if (highlightedIndex >= 0 && items[highlightedIndex]) {
                e.preventDefault();
                const val = items[highlightedIndex].getAttribute('data-val');
                seleccionarCategoria(val);
            }
        } else if (e.key === 'Escape') {
            menuCat.classList.add('hidden');
        }
    });

    function updateHighlight(items) {
        items.forEach((item, idx) => {
            if (idx === highlightedIndex) {
                item.classList.add('bg-emerald-100', 'text-emerald-900');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-emerald-100', 'text-emerald-900');
            }
        });
    }

    inputCat.addEventListener('blur', function() {
        setTimeout(() => {
            menuCat.classList.add('hidden');
        }, 220);
    });

    window.seleccionarCategoria = function(cat) {
        inputCat.value = cat;
        menuCat.classList.add('hidden');
        inputCat.setCustomValidity('');
        inputCat.classList.remove('border-red-500');
        inputCat.focus();
    };
}

// ── Autocomplete interactivo de proveedores ──
const proveedoresDisponibles = @json($proveedores);

function initProveedorAutocomplete() {
    const inputNombre = document.getElementById('inputProveedorNombre');
    const inputId     = document.getElementById('inputProveedorId');
    const menuPrv     = document.getElementById('proveedorAutocompleteList');
    if (!inputNombre || !inputId || !menuPrv) return;

    let highlightedIndex = -1;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderSugerencias(filtro = '') {
        const query = filtro.trim().toLowerCase();
        const matches = query === ''
            ? proveedoresDisponibles.slice(0, 8)
            : proveedoresDisponibles.filter(p => {
                const n = (p.nombre || '').toLowerCase();
                const c = (p.codigo || '').toLowerCase();
                return n.includes(query) || c.includes(query);
            }).slice(0, 10);

        highlightedIndex = -1;

        if (matches.length === 0) {
            menuPrv.innerHTML = `
                <div class="px-4 py-2.5 text-xs text-gray-500 italic bg-gray-50">
                    No se encontró ningún proveedor con ese nombre o código.
                </div>`;
            menuPrv.classList.remove('hidden');
            return;
        }

        menuPrv.innerHTML = matches.map((p, idx) => `
            <div class="proveedor-suggestion-item px-4 py-2.5 text-sm cursor-pointer transition flex items-center justify-between hover:bg-emerald-50 hover:text-emerald-800 ${idx === 0 ? 'bg-gray-50/70' : ''}"
                 data-id="${p.id}"
                 data-nombre="${escapeHtml(p.nombre)}"
                 onmousedown="seleccionarProveedor(${p.id}, '${escapeHtml(p.nombre)}')">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="font-semibold text-gray-800">${escapeHtml(p.nombre)}</span>
                </div>
                ${p.codigo ? `<span class="text-[11px] font-mono px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 font-bold">${escapeHtml(p.codigo)}</span>` : '<span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Proveedor</span>'}
            </div>
        `).join('');

        menuPrv.classList.remove('hidden');
    }

    inputNombre.addEventListener('focus', function() {
        renderSugerencias(this.value);
    });

    inputNombre.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        const exactMatch = proveedoresDisponibles.find(p => p.nombre.toLowerCase() === query);
        if (exactMatch) {
            inputId.value = exactMatch.id;
        } else {
            inputId.value = '';
        }
        renderSugerencias(this.value);
    });

    inputNombre.addEventListener('keydown', function(e) {
        if (menuPrv.classList.contains('hidden')) return;

        const items = menuPrv.querySelectorAll('.proveedor-suggestion-item');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = (highlightedIndex + 1) % items.length;
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = (highlightedIndex - 1 + items.length) % items.length;
            updateHighlight(items);
        } else if (e.key === 'Enter') {
            if (highlightedIndex >= 0 && items[highlightedIndex]) {
                e.preventDefault();
                const id = items[highlightedIndex].getAttribute('data-id');
                const nombre = items[highlightedIndex].getAttribute('data-nombre');
                seleccionarProveedor(id, nombre);
            }
        } else if (e.key === 'Escape') {
            menuPrv.classList.add('hidden');
        }
    });

    function updateHighlight(items) {
        items.forEach((item, idx) => {
            if (idx === highlightedIndex) {
                item.classList.add('bg-emerald-100', 'text-emerald-900');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('bg-emerald-100', 'text-emerald-900');
            }
        });
    }

    inputNombre.addEventListener('blur', function() {
        setTimeout(() => {
            menuPrv.classList.add('hidden');
            if (!inputNombre.value.trim()) {
                inputId.value = '';
            } else if (!inputId.value) {
                const exact = proveedoresDisponibles.find(p => p.nombre.toLowerCase() === inputNombre.value.trim().toLowerCase());
                if (exact) {
                    inputId.value = exact.id;
                    inputNombre.value = exact.nombre;
                }
            }
        }, 220);
    });

    window.seleccionarProveedor = function(id, nombre) {
        inputId.value = id;
        inputNombre.value = nombre;
        menuPrv.classList.add('hidden');
        inputNombre.setCustomValidity('');
        inputNombre.classList.remove('border-red-500');
        inputNombre.focus();
    };
}

document.addEventListener('DOMContentLoaded', () => {
    initCategoriaAutocomplete();
    initProveedorAutocomplete();
});
</script>

</x-superadmin-layout>
