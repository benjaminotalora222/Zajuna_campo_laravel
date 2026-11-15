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
        <a id="sidebar-brand-full" href="{{ route('operativo.dashboard') }}" class="block">
            <img src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo" class="w-full max-w-[170px] h-auto object-contain">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('operativo.dashboard') }}" class="items-center justify-center hidden">
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
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-zmorado whitespace-nowrap">Panel Operativo</span>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1 overflow-x-hidden">

        @php $isDashboardOp = request()->routeIs('operativo.dashboard'); @endphp
        <a href="{{ route('operativo.dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isDashboardOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Panel general">
            @if($isDashboardOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isDashboardOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span class="sidebar-label">Panel general</span>
        </a>

        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">MINIMARKET</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        @php $isConsignacionOp = request()->routeIs('operativo.consignacion.*'); @endphp
        <a href="{{ route('operativo.consignacion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isConsignacionOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Inventario">
            @if($isConsignacionOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isConsignacionOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="sidebar-label">Inventario</span>
        </a>

        @if(auth()->check() && auth()->user()->tieneAccesoVentas())
        @php $isVentasOp = request()->routeIs('operativo.ventas.*'); @endphp
        <a href="{{ route('operativo.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isVentasOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Ventas">
            @if($isVentasOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isVentasOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <circle cx="8" cy="21" r="1.5"/>
                <circle cx="19" cy="21" r="1.5"/>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
            <span class="sidebar-label">Ventas</span>
        </a>
        @endif

        @php $isOrdenesOp = request()->routeIs('operativo.ordenes.*'); @endphp
        <a href="{{ route('operativo.ordenes.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isOrdenesOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Órdenes de pedido">
            @if($isOrdenesOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isOrdenesOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <span class="sidebar-label">Órdenes de pedido</span>
        </a>

        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">TRANSFERENCIAS Y SHOWS</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        @php $isCronogramaOp = request()->routeIs('operativo.cronograma.*'); @endphp
        <a href="{{ route('operativo.cronograma.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isCronogramaOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Cronograma de espacios">
            @if($isCronogramaOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isCronogramaOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span class="sidebar-label">Cronograma de espacios</span>
        </a>

        @php $isActividadesOp = request()->is('operativo/actividades*'); @endphp
        <a href="{{ route('operativo.actividades.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isActividadesOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Ejecución de actividades">
            @if($isActividadesOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isActividadesOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
            </svg>
            <span class="sidebar-label">Ejecución de actividades</span>
        </a>

        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">INVESTIGACIÓN</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        @php $isProyectosOp = request()->routeIs('operativo.proyectos.*'); @endphp
        <a href="{{ route('operativo.proyectos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isProyectosOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Proyectos">
            @if($isProyectosOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isProyectosOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M10 2v7.31L4.69 18.5A2 2 0 0 0 6.41 21h11.18a2 2 0 0 0 1.72-2.5L14 9.31V2"/>
                <line x1="8.5" y1="2" x2="15.5" y2="2"/>
                <line x1="7" y1="15" x2="17" y2="15"/>
            </svg>
            <span class="sidebar-label">Proyectos</span>
        </a>

        @php $isTareasOp = request()->is('operativo/tareas*') || request()->is('operativo/ejecucion*'); @endphp
        <a href="{{ route('operativo.tareas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isTareasOp ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Tareas y avances">
            @if($isTareasOp)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isTareasOp ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
            <span class="sidebar-label">Tareas y avances</span>
            @php
                $tareasVencidasSidebarOp = \App\Models\Tarea::vencidas()->count();
            @endphp
            @if($tareasVencidasSidebarOp > 0)
                <span class="sidebar-badge ml-auto bg-red-500 text-white text-[10.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0 animate-pulse"
                      title="{{ $tareasVencidasSidebarOp }} tarea(s) vencida(s)">
                    {{ $tareasVencidasSidebarOp }}
                </span>
            @endif
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
            <p class="text-[11px] text-gray-500 font-medium">Operativo</p>
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
