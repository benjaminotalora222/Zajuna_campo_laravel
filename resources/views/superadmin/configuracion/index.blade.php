@php $title = 'Configuración del Sistema'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold leading-tight">Configuración del Sistema</h1>
        <p class="text-sm mt-1" style="color:#5a5a4f;">Administra las preferencias y ajustes generales de la plataforma.</p>
    </div>

    {{-- ALERTA ÉXITO --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- TABS --}}
    <div x-data="{ tab: 'general' }">

        {{-- Tab bar --}}
        <div class="flex gap-1 mb-8 border-b" style="border-color:#e7e0cc;">
            <button @click="tab='general'"
                    :class="tab==='general' ? 'border-b-2 font-bold' : 'font-semibold opacity-60 hover:opacity-100'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm transition -mb-px"
                    style="border-color:#fdc300; color:#1c2b16;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>
                </svg>
                General
            </button>
            <button @click="tab='notificaciones'"
                    :class="tab==='notificaciones' ? 'border-b-2 font-bold' : 'font-semibold opacity-60 hover:opacity-100'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm transition -mb-px"
                    style="border-color:#fdc300; color:#1c2b16;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                Notificaciones
            </button>
            <button @click="tab='seguridad'"
                    :class="tab==='seguridad' ? 'border-b-2 font-bold' : 'font-semibold opacity-60 hover:opacity-100'"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm transition -mb-px"
                    style="border-color:#fdc300; color:#1c2b16;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                </svg>
                Seguridad
            </button>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('superadmin.configuracion.update') }}">
            @csrf

            {{-- ── TAB GENERAL ────────────────────────────────── --}}
            <div x-show="tab==='general'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border p-6 flex gap-6" style="border-color:#e7e0cc;">
                    {{-- Icono --}}
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:#fff7e6;">
                            <svg class="w-6 h-6" style="color:#fdc300;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-1.55-1H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.55V3a2 2 0 0 1 4 0v.09a1.7 1.7 0 0 0 1 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 1.55 1H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1Z"/>
                            </svg>
                        </div>
                    </div>
                    {{-- Texto y campos --}}
                    <div class="flex-1">
                        <h2 class="font-extrabold text-base mb-0.5">Ajustes Generales</h2>
                        <p class="text-xs mb-5" style="color:#5a5a4f;">Configura las preferencias básicas del sistema.</p>

                        <div class="divide-y" style="divide-color:#f3f0e8;">

                            {{-- Nombre de la Plataforma --}}
                            <div class="flex items-center justify-between py-4 gap-6">
                                <div>
                                    <p class="text-sm font-semibold">Nombre de la Plataforma</p>
                                    <p class="text-xs mt-0.5" style="color:#5a5a4f;">Este nombre se mostrará en todo el sistema.</p>
                                </div>
                                <input type="text" name="nombre_plataforma"
                                       value="{{ $config['nombre_plataforma'] ?? 'Zajuna Go' }}"
                                       class="w-56 px-3 py-2 rounded-xl border text-sm outline-none transition"
                                       style="border-color:#e7e0cc; background:#fafafa;"
                                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                            </div>

                            {{-- Zona Horaria --}}
                            <div class="flex items-center justify-between py-4 gap-6">
                                <div>
                                    <p class="text-sm font-semibold">Zona Horaria</p>
                                    <p class="text-xs mt-0.5" style="color:#5a5a4f;">Selecciona la zona horaria predeterminada para la plataforma.</p>
                                </div>
                                <select name="zona_horaria"
                                        class="w-56 px-3 py-2 rounded-xl border text-sm outline-none transition"
                                        style="border-color:#e7e0cc; background:#fafafa;">
                                    @foreach($zonas as $valor => $label)
                                        <option value="{{ $valor }}" {{ ($config['zona_horaria'] ?? 'America/Bogota') === $valor ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Idioma --}}
                            <div class="flex items-center justify-between py-4 gap-6">
                                <div>
                                    <p class="text-sm font-semibold">Idioma</p>
                                    <p class="text-xs mt-0.5" style="color:#5a5a4f;">Selecciona el idioma predeterminado para la plataforma.</p>
                                </div>
                                <select name="idioma"
                                        class="w-56 px-3 py-2 rounded-xl border text-sm outline-none transition"
                                        style="border-color:#e7e0cc; background:#fafafa;">
                                    @foreach($idiomas as $valor => $label)
                                        <option value="{{ $valor }}" {{ ($config['idioma'] ?? 'es') === $valor ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TAB NOTIFICACIONES ──────────────────────────── --}}
            <div x-show="tab==='notificaciones'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border p-6 flex gap-6" style="border-color:#e7e0cc;">
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:#f3e8ff;">
                            <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="font-extrabold text-base mb-0.5">Notificaciones</h2>
                        <p class="text-xs mb-5" style="color:#5a5a4f;">Gestiona las preferencias de notificaciones del sistema.</p>

                        <div class="divide-y" style="divide-color:#f3f0e8;">

                            @php
                                $toggleItems = [
                                    ['key' => 'notif_correo',           'label' => 'Notificaciones por correo',    'desc' => 'Recibe notificaciones importantes por correo electrónico.'],
                                    ['key' => 'notif_recordatorios',    'label' => 'Recordatorios de tareas',      'desc' => 'Recibe recordatorios sobre tareas y actividades pendientes.'],
                                    ['key' => 'notif_alertas_criticas', 'label' => 'Alertas críticas',             'desc' => 'Recibe alertas inmediatas sobre situaciones críticas.'],
                                ];
                            @endphp

                            @foreach($toggleItems as $item)
                            <div class="flex items-center justify-between py-4 gap-6">
                                <div>
                                    <p class="text-sm font-semibold">{{ $item['label'] }}</p>
                                    <p class="text-xs mt-0.5" style="color:#5a5a4f;">{{ $item['desc'] }}</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $item['key'] }}" value="1"
                                           class="sr-only peer"
                                           {{ ($config[$item['key']] ?? '1') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 rounded-full peer transition-colors
                                                bg-gray-200 peer-checked:bg-zdorado
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:transition-all peer-checked:after:translate-x-5">
                                    </div>
                                </label>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TAB SEGURIDAD ───────────────────────────────── --}}
            <div x-show="tab==='seguridad'" x-cloak>
                <div class="bg-white rounded-2xl shadow-sm border p-6 flex gap-6" style="border-color:#e7e0cc;">
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:#f3e8ff;">
                            <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="font-extrabold text-base mb-0.5">Seguridad</h2>
                        <p class="text-xs mb-5" style="color:#5a5a4f;">Configura las opciones de seguridad de la plataforma.</p>

                        <div class="divide-y" style="divide-color:#f3f0e8;">

                            @php
                                $segItems = [
                                    ['key' => 'seg_dos_pasos',         'label' => 'Autenticación en dos pasos', 'desc' => 'Añade una capa adicional de seguridad a tu cuenta.'],
                                    ['key' => 'seg_sesion_automatica', 'label' => 'Sesión automática',          'desc' => 'Mantener la sesión iniciada en este dispositivo.'],
                                ];
                            @endphp

                            @foreach($segItems as $item)
                            <div class="flex items-center justify-between py-4 gap-6">
                                <div>
                                    <p class="text-sm font-semibold">{{ $item['label'] }}</p>
                                    <p class="text-xs mt-0.5" style="color:#5a5a4f;">{{ $item['desc'] }}</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $item['key'] }}" value="1"
                                           class="sr-only peer"
                                           {{ ($config[$item['key']] ?? '0') === '1' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 rounded-full peer transition-colors
                                                bg-gray-200 peer-checked:bg-zdorado
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:transition-all peer-checked:after:translate-x-5">
                                    </div>
                                </label>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTÓN GUARDAR --}}
            <div class="flex justify-end mt-6">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm transition hover:opacity-90"
                        style="background:#fdc300; color:#71277a;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                        <path d="M17 21v-8H7v8M7 3v5h8"/>
                    </svg>
                    Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Alpine.js para los tabs --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</x-superadmin-layout>
