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
        #sidebar { transition: width 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar .sidebar-label { transition: opacity 0.2s ease, width 0.3s ease; white-space: nowrap; overflow: hidden; }
        #sidebar .sidebar-section-label { transition: opacity 0.2s ease, height 0.2s ease; overflow: hidden; }
        #sidebar-toggle-arrow { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; }
        #sidebar.collapsed .sidebar-section-label { opacity: 0; height: 0; padding: 0; }
        #sidebar.collapsed { width: 4rem; }
        #sidebar.collapsed #sidebar-brand-full { display: none; }
        #sidebar.collapsed #sidebar-brand-icon { display: flex; }
        #sidebar:not(.collapsed) #sidebar-brand-icon { display: none; }
        #sidebar.collapsed #sidebar-footer-text { display: none; }
        #sidebar.collapsed #sidebar-footer-logout { display: none; }
        .sidebar-nav-link { min-width: 0; }
        #sidebar.collapsed .sidebar-nav-link { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar nav::-webkit-scrollbar { display: none; }
        #sidebar nav { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>
<body class="flex min-h-screen bg-zcrema font-sans">

{{-- ============================================================
     SIDEBAR
     ============================================================ --}}
<aside id="sidebar" class="w-64 h-screen bg-zverde text-white flex flex-col shrink-0 sticky top-0 relative">

    {{-- Toggle button --}}
    <button id="sidebar-toggle"
            onclick="toggleSidebar()"
            class="absolute -right-3.5 top-6 z-50 w-7 h-7 rounded-full bg-zdorado text-zmorado flex items-center justify-center shadow-md hover:scale-110 transition-transform"
            title="Colapsar menú">
        <svg id="sidebar-toggle-arrow" class="w-3.5 h-3.5 rotated" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </button>

    {{-- Brand --}}
    <div class="flex items-center justify-center px-4 py-4 border-b border-white/15 overflow-hidden">
        <a id="sidebar-brand-full" href="{{ route('operativo.dashboard') }}" class="block">
            <img src="{{ asset('img/logo-blanco.png') }}" alt="Zajuna Campo" class="w-full max-w-[160px] h-auto">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('operativo.dashboard') }}" class="items-center justify-center hidden">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center font-extrabold text-sm">Z</div>
        </a>
    </div>

    <p class="sidebar-section-label px-5 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/55">
        Panel Operativo
    </p>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-5 space-y-1 overflow-x-hidden">

        <a href="{{ route('operativo.dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white text-sm font-semibold relative
                  {{ request()->routeIs('operativo.dashboard') ? 'bg-zmorado' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition"
           title="Panel general">
            @if(request()->routeIs('operativo.dashboard'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span class="sidebar-label">Panel general</span>
        </a>

        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Minimarket</p>

        <a href="{{ route('operativo.consignacion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('operativo.consignacion.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Inventario y consignación">
            @if(request()->routeIs('operativo.consignacion.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Inventario y consignación</span>
        </a>

        <a href="{{ route('operativo.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('operativo.ventas.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Ventas">
            @if(request()->routeIs('operativo.ventas.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span class="sidebar-label">Ventas</span>
        </a>

        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Transferencias y shows</p>

        <a href="{{ route('operativo.cronograma.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('operativo.cronograma.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Cronograma de espacios">
            @if(request()->routeIs('operativo.cronograma.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
            </svg>
            <span class="sidebar-label">Cronograma de espacios</span>
        </a>

        <a href="{{ route('operativo.actividades.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('operativo/actividades*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Ejecución de actividades">
            @if(request()->is('operativo/actividades*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
            </svg>
            <span class="sidebar-label">Ejecución de actividades</span>
        </a>

        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Investigación</p>

        <a href="{{ route('operativo.proyectos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('operativo.proyectos.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Proyectos">
            @if(request()->routeIs('operativo.proyectos.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
            </svg>
            <span class="sidebar-label">Proyectos</span>
        </a>

        <a href="{{ route('operativo.tareas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('operativo/tareas*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Tareas y avances">
            @if(request()->is('operativo/tareas*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
            <span class="sidebar-label">Tareas y avances</span>
        </a>

    </nav>

    {{-- Footer --}}
    <div class="flex items-center gap-2.5 px-5 py-4 border-t border-white/15 overflow-hidden">
        <div class="w-9 h-9 rounded-full bg-zmorado flex items-center justify-center font-extrabold text-sm shrink-0">
            @php
                $parts = explode(' ', auth()->user()->name ?? 'U U');
                echo strtoupper(substr($parts[0], 0, 1)) . strtoupper(substr($parts[1] ?? 'U', 0, 1));
            @endphp
        </div>
        <div id="sidebar-footer-text" class="leading-tight overflow-hidden flex-1 min-w-0">
            <p class="text-[13px] font-bold truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
            <p class="text-[11px] text-white/60">Operativo</p>
        </div>
        <form id="sidebar-footer-logout" method="POST" action="{{ route('logout') }}" class="shrink-0">
            @csrf
            <button type="submit"
                    class="w-8 h-8 rounded-lg bg-white/10 text-white/80 flex items-center justify-center hover:bg-zdorado hover:text-zmorado transition"
                    title="Cerrar sesión">
                <svg class="w-4 h-4 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
                </svg>
            </button>
        </form>
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
