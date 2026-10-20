<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal' }} — Zajuna Campo</title>
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
        #sidebar .sidebar-section-label {
            transition: opacity 0.15s ease, height 0.3s ease;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        #sidebar .sidebar-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.18);
        }
        #sidebar .sidebar-section-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3.5px 10px;
            border-radius: 9999px;
            background: rgba(14, 46, 2, 0.38);
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            white-space: nowrap;
        }
        #sidebar .sidebar-section-pill::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background: #fdc300;
            display: inline-block;
            flex-shrink: 0;
            box-shadow: 0 0 4px rgba(253, 195, 0, 0.6);
        }
        #sidebar-toggle-arrow { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar-toggle-arrow.rotated { transform: rotate(180deg); }

        #sidebar.collapsed { width: 4rem; }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; display: none; }
        #sidebar.collapsed .sidebar-section-label { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
        #sidebar.collapsed #sidebar-brand-full { display: none; }
        #sidebar.collapsed #sidebar-brand-icon { display: flex; }
        #sidebar:not(.collapsed) #sidebar-brand-icon { display: none; }
        #sidebar.collapsed #sidebar-footer-text { display: none; }
        #sidebar.collapsed #sidebar-footer-logout { display: none; }

        #sidebar.collapsed nav { padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .sidebar-nav-link {
            justify-content: center !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            gap: 0 !important;
        }
        #sidebar.collapsed > div:last-child { justify-content: center; padding-left: 0; padding-right: 0; }

        #sidebar nav::-webkit-scrollbar { display: none; }
        #sidebar nav { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>
<body class="flex min-h-screen bg-zcrema font-sans">

{{-- ============================================================
     SIDEBAR PROVEEDOR
     ============================================================ --}}
<aside id="sidebar" class="w-64 h-screen bg-zverde text-white flex flex-col shrink-0 sticky top-0 relative">

    {{-- Toggle button --}}
    <button onclick="toggleSidebar()"
            class="absolute -right-3.5 top-6 z-50 w-7 h-7 rounded-full bg-zdorado text-zmorado flex items-center justify-center shadow-md hover:scale-110 transition-transform">
        <svg id="sidebar-toggle-arrow" class="w-3.5 h-3.5 rotated" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </button>

    {{-- Brand --}}
    <div class="flex items-center justify-center px-4 py-4 border-b border-white/15 overflow-hidden">
        <a id="sidebar-brand-full" href="{{ route('proveedor.dashboard') }}" class="block">
            <img src="{{ asset('img/logo-blanco.png') }}" alt="Zajuna Campo" class="w-full max-w-[160px] h-auto">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('proveedor.dashboard') }}" class="items-center justify-center hidden">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center font-extrabold text-sm">Z</div>
        </a>
    </div>

    {{-- Badge solo lectura --}}
    <div class="sidebar-section-label mx-3 mt-3 mb-1 px-3 py-1.5 rounded-lg flex items-center gap-2"
         style="background:rgba(253,195,0,0.15); border:1px solid rgba(253,195,0,0.3);">
        <svg class="w-3.5 h-3.5 shrink-0" style="color:#fdc300;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span class="text-[11px] font-bold" style="color:#fdc300;">Portal de solo lectura</span>
    </div>

    <div class="sidebar-section-label px-4 pt-3 pb-2">
        <span class="sidebar-section-pill">Portal Proveedor</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-5 space-y-1 overflow-x-hidden">

        {{-- Dashboard --}}
        <a href="{{ route('proveedor.dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('proveedor.dashboard') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Panel general">
            @if(request()->routeIs('proveedor.dashboard'))
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

        <div class="sidebar-section-label px-2 pt-4 pb-1.5">
            <span class="sidebar-section-pill">Mis productos</span>
        </div>

        {{-- Ventas --}}
        <a href="{{ route('proveedor.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('proveedor.ventas.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Mis ventas">
            @if(request()->routeIs('proveedor.ventas.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span class="sidebar-label">Mis ventas</span>
        </a>

        {{-- Inventario --}}
        <a href="{{ route('proveedor.inventario.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('proveedor.inventario.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Mi inventario">
            @if(request()->routeIs('proveedor.inventario.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Mi inventario</span>
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
            <p class="text-[11px] text-white/60">Proveedor</p>
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
    @php
        $provAuth = auth()->user()->proveedor_id ? \App\Models\Proveedor::find(auth()->user()->proveedor_id) : null;
        $inactivoProv = !auth()->user()->activo || ($provAuth && !$provAuth->estado);
    @endphp

    @if($inactivoProv)
    <div class="m-6 p-4 rounded-2xl flex items-center gap-3.5 text-sm shadow-sm"
         style="background:#fffbeb; border:1px solid #fde68a; color:#92400e;">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fef3c7;">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div>
            <p class="font-extrabold text-amber-900">Cuenta pendiente de activación</p>
            <p class="text-xs text-amber-800 mt-0.5">El administrador debe activar tu cuenta para que puedas operar completamente en la plataforma.</p>
        </div>
    </div>
    @endif

    {{ $slot }}
</main>

<script>
(function() {
    const sidebar  = document.getElementById('sidebar');
    const arrow    = document.getElementById('sidebar-toggle-arrow');
    const KEY      = 'zajuna_sidebar_collapsed';

    function applyState(collapsed) {
        collapsed ? sidebar.classList.add('collapsed') : sidebar.classList.remove('collapsed');
        collapsed ? arrow.classList.remove('rotated') : arrow.classList.add('rotated');
    }

    applyState(localStorage.getItem(KEY) === '1');

    window.toggleSidebar = function() {
        const c = sidebar.classList.contains('collapsed');
        applyState(!c);
        localStorage.setItem(KEY, !c ? '1' : '0');
    };
})();
</script>

</body>
</html>
