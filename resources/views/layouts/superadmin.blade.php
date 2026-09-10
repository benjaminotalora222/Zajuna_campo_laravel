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
        #sidebar .sidebar-label { transition: opacity 0.15s ease; white-space: nowrap; overflow: hidden; }
        #sidebar .sidebar-section-label { transition: opacity 0.15s ease, height 0.3s ease; overflow: hidden; }
        #sidebar .sidebar-badge { transition: opacity 0.15s ease; }
        #sidebar-toggle-arrow { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar-toggle-arrow.rotated { transform: rotate(180deg); }

        /* Collapsed state */
        #sidebar.collapsed { width: 4rem; }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; display: none; }
        #sidebar.collapsed .sidebar-section-label { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; }
        #sidebar.collapsed .sidebar-badge { opacity: 0; width: 0; overflow: hidden; margin: 0; padding: 0; }
        #sidebar.collapsed #sidebar-brand-full { display: none; }
        #sidebar.collapsed #sidebar-brand-icon { display: flex; }
        #sidebar:not(.collapsed) #sidebar-brand-icon { display: none; }
        #sidebar.collapsed #sidebar-footer-text { display: none; }
        #sidebar.collapsed #sidebar-footer-logout { display: none; }

        /* Center icons when collapsed */
        #sidebar.collapsed nav { padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .sidebar-nav-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
        }

        /* Footer center */
        #sidebar.collapsed > div:last-child { justify-content: center; padding-left: 0; padding-right: 0; }

        /* Hide scrollbar */
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
        <a id="sidebar-brand-full" href="{{ route('dashboard') }}" class="block">
            <img src="{{ asset('img/logo-blanco.png') }}" alt="Zajuna Campo" class="w-full max-w-[160px] h-auto">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('dashboard') }}" class="items-center justify-center hidden">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center font-extrabold text-sm">Z</div>
        </a>
    </div>

    <p class="sidebar-section-label px-5 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/55">
        Panel Superadministrador
    </p>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-5 space-y-1 overflow-x-hidden">

        {{-- Panel general --}}
        <a href="{{ route('dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-white text-sm font-semibold relative
                  {{ request()->routeIs('dashboard') ? 'bg-zmorado' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition group"
           title="Panel general">
            @if(request()->routeIs('dashboard'))
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

        {{-- Minimarket --}}
        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Minimarket</p>

        <a href="{{ route('superadmin.proveedores.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.proveedores.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Proveedores">
            @if(request()->routeIs('superadmin.proveedores.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Proveedores</span>
        </a>

        <a href="{{ route('superadmin.productos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.productos.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Productos">
            @if(request()->routeIs('superadmin.productos.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
            </svg>
            <span class="sidebar-label">Productos</span>
        </a>

        <a href="{{ route('superadmin.categorias.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.categorias.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Categorías">
            @if(request()->routeIs('superadmin.categorias.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h7"/>
                <circle cx="17" cy="18" r="3"/><path d="m19.5 15.5-5 5"/>
            </svg>
            <span class="sidebar-label">Categorías</span>
        </a>

        <a href="{{ route('superadmin.consignacion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.consignacion.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Inventario y consignación">
            @if(request()->routeIs('superadmin.consignacion.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Inventario y consignación</span>
            @php $proximosVencer = \App\Models\Consignacion::where('estado','proximo_vencer')->count(); @endphp
            @if($proximosVencer > 0)
                <span class="sidebar-badge ml-auto bg-zdorado text-zmorado text-[10.5px] font-extrabold px-2 py-0.5 rounded-full shrink-0">{{ $proximosVencer }}</span>
            @endif
        </a>

        <a href="{{ route('superadmin.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.ventas.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Ventas">
            @if(request()->routeIs('superadmin.ventas.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span class="sidebar-label">Ventas</span>
        </a>

        {{-- Transferencias y shows --}}
        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Transferencias y shows</p>

        <a href="{{ route('superadmin.cronograma.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.cronograma.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Cronograma de espacios">
            @if(request()->routeIs('superadmin.cronograma.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
            </svg>
            <span class="sidebar-label">Cronograma de espacios</span>
        </a>

        <a href="{{ route('superadmin.actividades.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('superadmin/actividades*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Ejecución de actividades">
            @if(request()->is('superadmin/actividades*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
            </svg>
            <span class="sidebar-label">Ejecución de actividades</span>
        </a>

        {{-- Investigación --}}
        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Investigación</p>

        <a href="{{ route('superadmin.proyectos.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.proyectos.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Proyectos">
            @if(request()->routeIs('superadmin.proyectos.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
            </svg>
            <span class="sidebar-label">Proyectos</span>
        </a>

        <a href="{{ route('superadmin.tareas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('superadmin/tareas') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Tareas y avances">
            @if(request()->is('superadmin/tareas'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
            <span class="sidebar-label">Tareas y avances</span>
        </a>

        {{-- Administración --}}
        <p class="sidebar-section-label px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Administración</p>

        <a href="{{ route('superadmin.usuarios.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.usuarios.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Usuarios y roles">
            @if(request()->routeIs('superadmin.usuarios.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/>
                <circle cx="8.5" cy="7" r="3"/>
                <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
            </svg>
            <span class="sidebar-label">Usuarios y roles</span>
        </a>

        <a href="{{ route('superadmin.reportes.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.reportes.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Reportes y dashboards">
            @if(request()->routeIs('superadmin.reportes.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 4v16M16 4v4"/>
            </svg>
            <span class="sidebar-label">Reportes y dashboards</span>
        </a>

        <a href="{{ route('superadmin.auditoria.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.auditoria.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Registro de auditoría">
            @if(request()->routeIs('superadmin.auditoria.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z"/>
            </svg>
            <span class="sidebar-label">Registro de auditoría</span>
        </a>

        <a href="{{ route('superadmin.configuracion.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.configuracion.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Configuración del sistema">
            @if(request()->routeIs('superadmin.configuracion.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>
            </svg>
            <span class="sidebar-label">Configuración del sistema</span>
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
            <p class="text-[11px] text-white/60">Superadministrador</p>
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
