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
                    fontFamily: {
                        sans: ['Work Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="flex min-h-screen bg-zcrema font-sans">

{{-- ============================================================
     SIDEBAR
     ============================================================ --}}
<aside class="w-64 min-h-screen bg-zverde text-white flex flex-col shrink-0">

    {{-- Brand --}}
    <div class="flex items-center justify-center px-4 py-4 border-b border-white/15">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('img/logo-blanco.png') }}" alt="Zajuna Campo" class="w-full max-w-[200px] h-auto">
        </a>
    </div>

    <p class="px-5 pt-4 pb-2 text-[11px] font-bold uppercase tracking-wider text-white/55">
        Panel Superadministrador
    </p>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 pb-5 space-y-1">

        {{-- Panel general --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white text-sm font-semibold relative
                  {{ request()->routeIs('dashboard') ? 'bg-zmorado' : 'text-white/90 hover:bg-white/10 hover:text-white' }} transition">
            @if(request()->routeIs('dashboard'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                <rect x="3" y="16" width="7" height="5" rx="1.5"/>
            </svg>
            Panel general
        </a>

        {{-- Minimarket --}}
        <p class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Minimarket</p>

        <a href="{{ route('superadmin.proveedores.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.proveedores.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.proveedores.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
            </svg>
            Proveedores
        </a>

        <a href="{{ route('superadmin.productos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.productos.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.productos.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
            </svg>
            Productos
        </a>

        <a href="{{ route('superadmin.categorias.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.categorias.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.categorias.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h7"/>
                <circle cx="17" cy="18" r="3"/><path d="m19.5 15.5-5 5"/>
            </svg>
            Categorías
        </a>

        <a href="{{ route('superadmin.consignacion.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.consignacion.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.consignacion.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
            </svg>
            Inventario y consignación
            @php $proximosVencer = \App\Models\Consignacion::where('estado','proximo_vencer')->count(); @endphp
            @if($proximosVencer > 0)
                <span class="ml-auto bg-zdorado text-zmorado text-[10.5px] font-extrabold px-2 py-0.5 rounded-full">{{ $proximosVencer }}</span>
            @endif
        </a>

        <a href="{{ route('superadmin.ventas.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.ventas.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.ventas.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
            </svg>
            Ventas
        </a>

        {{-- Transferencias y shows --}}
        <p class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Transferencias y shows</p>

        <a href="{{ route('superadmin.cronograma.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.cronograma.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.cronograma.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
            </svg>
            Cronograma de espacios
        </a>

        <a href="{{ route('superadmin.actividades.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('superadmin/actividades*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->is('superadmin/actividades*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
            </svg>
            Ejecución de actividades
        </a>

        {{-- Investigación --}}
        <p class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Investigación</p>

        <a href="{{ route('superadmin.proyectos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.proyectos.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.proyectos.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
            </svg>
            Proyectos
        </a>

        <a href="{{ route('superadmin.tareas.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->is('superadmin/tareas') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->is('superadmin/tareas'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
            </svg>
            Tareas y avances
        </a>

        {{-- Administración --}}
        <p class="px-3 pt-4 pb-1 text-[11px] font-bold uppercase tracking-wider text-white/45">Administración</p>

        <a href="{{ route('superadmin.usuarios.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.usuarios.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.usuarios.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/>
                <circle cx="8.5" cy="7" r="3"/>
                <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
            </svg>
            Usuarios y roles
        </a>

        <a href="{{ route('superadmin.reportes.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold relative transition
                  {{ request()->routeIs('superadmin.reportes.*') ? 'bg-zmorado text-white' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
            @if(request()->routeIs('superadmin.reportes.*'))
                <span class="absolute -left-3 top-1/2 -translate-y-1/2 w-1 h-5 bg-zdorado rounded-r"></span>
            @endif
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 4v16M16 4v4"/>
            </svg>
            Reportes y dashboards
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 hover:text-white text-sm font-semibold transition">
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <path d="M4 6h16M4 6l1 13a2 2 0 0 0 2 1.8h10A2 2 0 0 0 19 19L20 6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/>
            </svg>
            Registro de auditoría
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/90 hover:bg-white/10 hover:text-white text-sm font-semibold transition">
            <svg class="w-[18px] h-[18px] stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>
            </svg>
            Configuración del sistema
        </a>

    </nav>

    {{-- Footer --}}
    <div class="flex items-center gap-2.5 px-5 py-4 border-t border-white/15">
        <div class="w-9 h-9 rounded-full bg-zmorado flex items-center justify-center font-extrabold text-sm shrink-0">
            @php
                $parts = explode(' ', auth()->user()->name ?? 'U U');
                echo strtoupper(substr($parts[0], 0, 1)) . strtoupper(substr($parts[1] ?? 'U', 0, 1));
            @endphp
        </div>
        <div class="leading-tight overflow-hidden flex-1">
            <p class="text-[13px] font-bold truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
            <p class="text-[11px] text-white/60">Superadministrador</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
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

{{-- ============================================================
     CONTENIDO PRINCIPAL
     ============================================================ --}}
<main class="flex-1 min-h-screen overflow-auto bg-zcrema">
    {{ $slot }}
</main>

</body>
</html>
