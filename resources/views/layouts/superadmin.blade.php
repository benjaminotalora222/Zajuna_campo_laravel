<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel' }} — Zajuna Campo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        zverde:  '#39a900',
                        zmorado: '#71277a',
                        zdorado: '#fdc300',
                        zcrema:  '#fdf9ee',
                    },
                    fontFamily: { sans: ['Work Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        #sidebar { transition: width 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar .sidebar-label { transition: opacity 0.15s ease; white-space: nowrap; overflow: hidden; }
        #sidebar .sidebar-section-divider {
            transition: opacity 0.15s ease, height 0.3s ease;
            overflow: hidden;
        }
        #sidebar .sidebar-role-pill {
            transition: opacity 0.15s ease, height 0.3s ease;
            overflow: hidden;
        }
        #sidebar .sidebar-badge { transition: opacity 0.15s ease; }
        #sidebar-toggle-arrow { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar-toggle-arrow.rotated { transform: rotate(180deg); }

        /* Collapsed state */
        #sidebar.collapsed { width: 4.5rem; }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; display: none; }
        #sidebar.collapsed .sidebar-section-divider { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
        #sidebar.collapsed .sidebar-role-pill { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
        #sidebar.collapsed .sidebar-badge { opacity: 0; width: 0; overflow: hidden; margin: 0; padding: 0; }
        #sidebar.collapsed #sidebar-brand-full { display: none; }
        #sidebar.collapsed #sidebar-brand-icon { display: flex; }
        #sidebar:not(.collapsed) #sidebar-brand-icon { display: none; }
        #sidebar.collapsed #sidebar-footer-text { display: none; }
        #sidebar.collapsed #sidebar-footer-logout { display: none; }
        #sidebar.collapsed #sidebar-bottom-wave { display: none; }

        /* Center icons when collapsed */
        #sidebar.collapsed nav { padding-left: 0.5rem; padding-right: 0.5rem; }
        #sidebar.collapsed .sidebar-nav-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
        }

        /* Footer center */
        #sidebar.collapsed .sidebar-footer { justify-content: center; padding-left: 0; padding-right: 0; }

        /* Hide scrollbar */
        #sidebar nav::-webkit-scrollbar { display: none; }
        #sidebar nav { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>
<body class="flex min-h-screen bg-zcrema font-sans">

{{-- ============================================================
     SIDEBAR
     ============================================================ --}}
<aside id="sidebar" class="w-64 h-screen bg-white text-gray-900 flex flex-col shrink-0 sticky top-0 relative border-r border-[#ece8dd] shadow-xs">

    {{-- Toggle button --}}
    <button id="sidebar-toggle"
            onclick="toggleSidebar()"
            class="absolute -right-3.5 top-6 z-50 w-7 h-7 rounded-full bg-zdorado text-gray-900 flex items-center justify-center shadow-md hover:scale-110 transition-transform"
            title="Colapsar menú">
        <svg id="sidebar-toggle-arrow" class="w-3.5 h-3.5 rotated text-gray-900" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </button>

    {{-- Brand --}}
    <div class="flex items-center justify-center px-4 pt-4 pb-2 overflow-hidden">
        <a id="sidebar-brand-full" href="{{ route('dashboard') }}" class="block">
            <img src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo" class="w-full max-w-[170px] h-auto object-contain">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('dashboard') }}" class="items-center justify-center hidden">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-zmorado border border-purple-200 flex items-center justify-center font-extrabold text-sm shadow-xs">
                ZC
            </div>
        </a>
    </div>

    {{-- Role Pill --}}
    <div class="sidebar-role-pill px-4 pt-1 pb-3">
        <div class="border-2 border-zmorado rounded-full py-1.5 px-3 flex items-center justify-center gap-2 bg-white shadow-xs">
            <span class="w-4 h-4 rounded-full bg-zmorado text-white flex items-center justify-center shrink-0">
                <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1.06 13.54L7.4 12l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41-5.64 5.66z"/>
                </svg>
            </span>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-zmorado whitespace-nowrap">Panel Superadministrador</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1 overflow-x-hidden">

        {{-- Panel general --}}
        @php $isDashboard = request()->routeIs('dashboard'); @endphp
        <a href="{{ route('dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isDashboard ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Panel general">
            @if($isDashboard)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isDashboard ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span class="sidebar-label">Panel general</span>
        </a>

        {{-- Minimarket --}}
        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">MINIMARKET</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        {{-- Proveedores --}}
        @php $isProveedores = request()->routeIs('superadmin.proveedores.*'); @endphp
        <a href="{{ route('superadmin.proveedores.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isProveedores ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Proveedores">
            @if($isProveedores)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isProveedores ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Proveedores</span>
        </a>

        {{-- Productos --}}
        @php $isProductos = request()->routeIs('superadmin.productos.*'); @endphp
        <a href="{{ route('superadmin.productos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isProductos ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Productos">
            @if($isProductos)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isProductos ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                <line x1="12" y1="22.08" x2="12" y2="12"/>
            </svg>
            <span class="sidebar-label">Productos</span>
        </a>

        {{-- Categorías --}}
        @php $isCategorias = request()->routeIs('superadmin.categorias.*'); @endphp
        <a href="{{ route('superadmin.categorias.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isCategorias ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Categorías">
            @if($isCategorias)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isCategorias ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="15" y2="6"/>
                <line x1="3" y1="12" x2="15" y2="12"/>
                <line x1="3" y1="18" x2="9" y2="18"/>
                <line x1="16" y1="18" x2="22" y2="18"/>
                <line x1="19" y1="15" x2="19" y2="21"/>
            </svg>
            <span class="sidebar-label">Categorías</span>
        </a>

        {{-- Inventario --}}
        @php $isConsignacion = request()->routeIs('superadmin.consignacion.*'); @endphp
        <a href="{{ route('superadmin.consignacion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isConsignacion ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Inventario">
            @if($isConsignacion)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isConsignacion ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="sidebar-label">Inventario</span>
            @php $proximosVencer = \App\Models\Consignacion::where('estado','proximo_vencer')->count(); @endphp
            @if($proximosVencer > 0)
                <span class="sidebar-badge ml-auto bg-zdorado text-gray-900 text-[10.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0 shadow-xs">{{ $proximosVencer }}</span>
            @endif
        </a>

        {{-- Ventas --}}
        @php $isVentas = request()->routeIs('superadmin.ventas.*'); @endphp
        <a href="{{ route('superadmin.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isVentas ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Ventas">
            @if($isVentas)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isVentas ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <circle cx="8" cy="21" r="1.5"/>
                <circle cx="19" cy="21" r="1.5"/>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
            <span class="sidebar-label">Ventas</span>
        </a>

        {{-- Órdenes de pedido --}}
        @php $isOrdenes = request()->routeIs('superadmin.ordenes.*'); @endphp
        <a href="{{ route('superadmin.ordenes.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isOrdenes ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Órdenes de pedido">
            @if($isOrdenes)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isOrdenes ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <span class="sidebar-label">Órdenes de pedido</span>
        </a>

        {{-- Transferencias y shows --}}
        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">TRANSFERENCIAS Y SHOWS</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        {{-- Cronograma de espacios --}}
        @php $isCronograma = request()->routeIs('superadmin.cronograma.*'); @endphp
        <a href="{{ route('superadmin.cronograma.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isCronograma ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Cronograma de espacios">
            @if($isCronograma)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isCronograma ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span class="sidebar-label">Cronograma de espacios</span>
        </a>

        {{-- Ejecución de actividades --}}
        @php $isActividades = request()->is('superadmin/actividades*'); @endphp
        <a href="{{ route('superadmin.actividades.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isActividades ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Ejecución de actividades">
            @if($isActividades)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isActividades ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
            </svg>
            <span class="sidebar-label">Ejecución de actividades</span>
        </a>

        {{-- Investigación --}}
        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">INVESTIGACIÓN</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        {{-- Proyectos --}}
        @php $isProyectos = request()->routeIs('superadmin.proyectos.*'); @endphp
        <a href="{{ route('superadmin.proyectos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isProyectos ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Proyectos">
            @if($isProyectos)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isProyectos ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M10 2v7.31L4.69 18.5A2 2 0 0 0 6.41 21h11.18a2 2 0 0 0 1.72-2.5L14 9.31V2"/>
                <line x1="8.5" y1="2" x2="15.5" y2="2"/>
                <line x1="7" y1="15" x2="17" y2="15"/>
            </svg>
            <span class="sidebar-label">Proyectos</span>
        </a>

        {{-- Tareas y avances --}}
        @php $isTareas = request()->is('superadmin/tareas*') || request()->is('superadmin/ejecucion*'); @endphp
        <a href="{{ route('superadmin.tareas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isTareas ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Tareas y avances">
            @if($isTareas)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isTareas ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
            <span class="sidebar-label">Tareas y avances</span>
            @php
                $tareasVencidasSidebar = \App\Models\Tarea::vencidas()->count();
            @endphp
            @if($tareasVencidasSidebar > 0)
                <span class="sidebar-badge ml-auto bg-red-500 text-white text-[10.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0 animate-pulse"
                      title="{{ $tareasVencidasSidebar }} tarea(s) vencida(s)">
                    {{ $tareasVencidasSidebar }}
                </span>
            @endif
        </a>

        {{-- Administración --}}
        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">ADMINISTRACIÓN</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        {{-- Usuarios y roles --}}
        @php $isUsuarios = request()->routeIs('superadmin.usuarios.*'); @endphp
        <a href="{{ route('superadmin.usuarios.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isUsuarios ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Usuarios y roles">
            @if($isUsuarios)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isUsuarios ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/>
                <circle cx="8.5" cy="7" r="3"/>
                <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
            </svg>
            <span class="sidebar-label">Usuarios y roles</span>
        </a>

        {{-- Reportes y dashboards --}}
        @php $isReportes = request()->routeIs('superadmin.reportes.*'); @endphp
        <a href="{{ route('superadmin.reportes.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isReportes ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Reportes y dashboards">
            @if($isReportes)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isReportes ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 4v16M16 4v4"/>
            </svg>
            <span class="sidebar-label">Reportes y dashboards</span>
        </a>

        {{-- Registro de auditoría --}}
        @php $isAuditoria = request()->routeIs('superadmin.auditoria.*'); @endphp
        <a href="{{ route('superadmin.auditoria.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isAuditoria ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Registro de auditoría">
            @if($isAuditoria)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isAuditoria ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
            </svg>
            <span class="sidebar-label">Registro de auditoría</span>
        </a>

        {{-- Configuración del sistema --}}
        @php $isConfiguracion = request()->routeIs('superadmin.configuracion.*'); @endphp
        <a href="{{ route('superadmin.configuracion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isConfiguracion ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Configuración del sistema">
            @if($isConfiguracion)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isConfiguracion ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>
            </svg>
            <span class="sidebar-label">Configuración del sistema</span>
        </a>

    </nav>

    {{-- Footer User Profile --}}
    <div class="sidebar-footer flex items-center gap-2.5 px-4 py-3 border-t border-gray-100 overflow-hidden bg-white shrink-0">
        <div class="w-9 h-9 rounded-full bg-zmorado text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-xs">
            @php
                $parts = explode(' ', auth()->user()->name ?? 'U U');
                echo strtoupper(substr($parts[0], 0, 1)) . strtoupper(substr($parts[1] ?? 'U', 0, 1));
            @endphp
        </div>
        <div id="sidebar-footer-text" class="leading-tight overflow-hidden flex-1 min-w-0">
            <p class="text-[13px] font-bold text-gray-900 truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
            <p class="text-[11px] text-gray-500 font-medium">Superadministrador</p>
        </div>
        <form id="sidebar-footer-logout" method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit"
                    class="w-9 h-9 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition"
                    title="Cerrar sesión">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Bottom Wave Ribbon --}}
    <div id="sidebar-bottom-wave" class="w-full overflow-hidden leading-none shrink-0" aria-hidden="true">
        <svg viewBox="0 0 260 46" class="w-full h-11 block" preserveAspectRatio="none">
            <path d="M0,18 C70,10 150,38 260,8 L260,46 L0,46 Z" fill="#71277a"/>
            <path d="M0,25 C75,18 155,42 260,16 L260,46 L0,46 Z" fill="#ffffff"/>
            <path d="M0,28 C80,21 160,44 260,20 L260,46 L0,46 Z" fill="#39a900"/>
            <path d="M0,37 C100,32 175,46 260,28 L260,46 L0,46 Z" fill="#fdc300"/>
        </svg>
    </div>

</aside>

{{-- CONTENIDO PRINCIPAL --}}
<main class="flex-1 min-h-screen overflow-auto bg-zcrema">
    {{ $slot }}
</main>

<script>
(function() {
    const sidebar  = document.getElementById('sidebar');
    const arrow    = document.getElementById('sidebar-toggle-arrow');
    const STORAGE_KEY = 'zajuna_sidebar_collapsed';

    function applyState(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            arrow.classList.remove('rotated');
        } else {
            sidebar.classList.remove('collapsed');
            arrow.classList.add('rotated');
        }
    }

    // Restore saved state
    applyState(localStorage.getItem(STORAGE_KEY) === '1');

    window.toggleSidebar = function() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        applyState(!isCollapsed);
        localStorage.setItem(STORAGE_KEY, !isCollapsed ? '1' : '0');
    };
})();
</script>

</body>
</html>
