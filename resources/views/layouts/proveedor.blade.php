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
        #sidebar .sidebar-readonly-badge {
            transition: opacity 0.15s ease, height 0.3s ease;
            overflow: hidden;
        }
        #sidebar-toggle-arrow { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar-toggle-arrow.rotated { transform: rotate(180deg); }

        /* Collapsed state */
        #sidebar.collapsed { width: 4.5rem; }
        #sidebar.collapsed .sidebar-label { opacity: 0; width: 0; display: none; }
        #sidebar.collapsed .sidebar-section-divider { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
        #sidebar.collapsed .sidebar-role-pill { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
        #sidebar.collapsed .sidebar-readonly-badge { opacity: 0; height: 0 !important; padding: 0 !important; margin: 0 !important; display: none !important; }
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
     SIDEBAR PROVEEDOR
     ============================================================ --}}
<aside id="sidebar" class="w-64 h-screen bg-white text-gray-900 flex flex-col shrink-0 sticky top-0 relative border-r border-[#ece8dd] shadow-xs">

    {{-- Toggle button --}}
    <button onclick="toggleSidebar()"
            class="absolute -right-3.5 top-6 z-50 w-7 h-7 rounded-full bg-zdorado text-gray-900 flex items-center justify-center shadow-md hover:scale-110 transition-transform"
            title="Colapsar menú">
        <svg id="sidebar-toggle-arrow" class="w-3.5 h-3.5 rotated text-gray-900" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path d="M15 18l-6-6 6-6"/>
        </svg>
    </button>

    {{-- Brand --}}
    <div class="flex items-center justify-center px-4 pt-4 pb-2 overflow-hidden">
        <a id="sidebar-brand-full" href="{{ route('proveedor.dashboard') }}" class="block">
            <img src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo" class="w-full max-w-[170px] h-auto object-contain">
        </a>
        <a id="sidebar-brand-icon" href="{{ route('proveedor.dashboard') }}" class="items-center justify-center hidden">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-zmorado border border-purple-200 flex items-center justify-center font-extrabold text-sm shadow-xs">
                ZC
            </div>
        </a>
    </div>

    {{-- Role Pill --}}
    <div class="sidebar-role-pill px-4 pt-1 pb-2">
        <div class="border-2 border-zmorado rounded-full py-1.5 px-3 flex items-center justify-center gap-2 bg-white shadow-xs">
            <span class="w-4 h-4 rounded-full bg-zmorado text-white flex items-center justify-center shrink-0">
                <svg class="w-2.5 h-2.5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm-1.06 13.54L7.4 12l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41-5.64 5.66z"/>
                </svg>
            </span>
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-zmorado whitespace-nowrap">Portal Proveedor</span>
        </div>
    </div>

    {{-- Badge solo lectura --}}
    <div class="sidebar-readonly-badge mx-4 mb-2 px-3 py-1.5 rounded-full flex items-center justify-center gap-2 bg-amber-50 border border-amber-200">
        <svg class="w-3.5 h-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>
        <span class="text-[10.5px] font-bold text-amber-800">Solo lectura</span>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1 overflow-x-hidden">

        {{-- Dashboard --}}
        @php $isDashboardProv = request()->routeIs('proveedor.dashboard'); @endphp
        <a href="{{ route('proveedor.dashboard') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isDashboardProv ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Panel general">
            @if($isDashboardProv)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isDashboardProv ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            <span class="sidebar-label">Panel general</span>
        </a>

        <div class="sidebar-section-divider flex items-center gap-2 px-3 pt-4 pb-1.5">
            <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-zverde">MIS PRODUCTOS</span>
            <span class="flex-1 h-[1.5px] bg-[#a7f3d0] rounded-full"></span>
        </div>

        {{-- Ventas --}}
        @php $isVentasProv = request()->routeIs('proveedor.ventas.*'); @endphp
        <a href="{{ route('proveedor.ventas.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isVentasProv ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Mis ventas">
            @if($isVentasProv)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isVentasProv ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            <span class="sidebar-label">Mis ventas</span>
        </a>

        {{-- Inventario --}}
        @php $isInventarioProv = request()->routeIs('proveedor.inventario.*'); @endphp
        <a href="{{ route('proveedor.inventario.index') }}"
           class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition relative group
                  {{ $isInventarioProv ? 'bg-zdorado text-gray-900 font-bold shadow-xs' : 'text-gray-800 font-semibold hover:bg-purple-50/70 hover:text-zmorado' }}"
           title="Mi inventario">
            @if($isInventarioProv)
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[19px] h-[19px] stroke-current fill-none stroke-2 shrink-0 {{ $isInventarioProv ? 'text-gray-900' : 'text-zmorado' }}" viewBox="0 0 24 24">
                <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
            </svg>
            <span class="sidebar-label">Mi inventario</span>
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
            <p class="text-[11px] text-gray-500 font-medium">Proveedor</p>
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
