@php
    $editing = isset($usuario);
    $title   = $editing ? 'Editar usuario' : 'Nuevo usuario';
@endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-2xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('superadmin.usuarios.index') }}"
           class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
           style="background:#f0fdf4; color:#39a900;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">{{ $title }}</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                {{ $editing ? 'Modifica los datos del usuario.' : 'Completa los datos para crear un nuevo usuario.' }}
            </p>
        </div>
    </div>

    {{-- TARJETA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        <form method="POST"
              action="{{ $editing ? route('superadmin.usuarios.update', $usuario) : route('superadmin.usuarios.store') }}">
            @csrf
            @if($editing) @method('PUT') @endif

            {{-- Nombre --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Nombre completo <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $usuario->name ?? '') }}"
                       placeholder="Ej. María Rodríguez"
                       class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                       style="border-color:{{ $errors->has('name') ? '#ef4444' : '#e7e0cc' }}; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900'; this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='{{ $errors->has('name') ? '#ef4444' : '#e7e0cc' }}'; this.style.boxShadow='none'">
                @error('name')
                    <p class="text-xs mt-1.5 font-semibold" style="color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Correo electrónico <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $usuario->email ?? '') }}"
                       placeholder="correo@ejemplo.com"
                       class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                       style="border-color:{{ $errors->has('email') ? '#ef4444' : '#e7e0cc' }}; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900'; this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='{{ $errors->has('email') ? '#ef4444' : '#e7e0cc' }}'; this.style.boxShadow='none'">
                @error('email')
                    <p class="text-xs mt-1.5 font-semibold" style="color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="password" class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                        Contraseña {{ $editing ? '' : '*' }}
                    </label>
                    @if($editing)
                        <p class="text-xs mb-2" style="color:#9a9a8a;">Deja en blanco para no cambiarla.</p>
                    @endif
                    <input type="password" id="password" name="password"
                           placeholder="{{ $editing ? 'Nueva contraseña (opcional)' : 'Mínimo 8 caracteres' }}"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:{{ $errors->has('password') ? '#ef4444' : '#e7e0cc' }}; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900'; this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='{{ $errors->has('password') ? '#ef4444' : '#e7e0cc' }}'; this.style.boxShadow='none'">
                    @error('password')
                        <p class="text-xs mt-1.5 font-semibold" style="color:#ef4444;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                        Confirmar contraseña
                    </label>
                    @if($editing) <p class="text-xs mb-2 opacity-0">-</p> @endif
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           placeholder="Repite la contraseña"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900'; this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc'; this.style.boxShadow='none'">
                </div>
            </div>

            {{-- Rol --}}
            <div class="mb-5">
                <label for="role_id" class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Rol <span style="color:#ef4444;">*</span>
                </label>
                <select id="role_id" name="role_id"
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                        style="border-color:{{ $errors->has('role_id') ? '#ef4444' : '#e7e0cc' }}; background:#fafafa;">
                    <option value="">Selecciona un rol...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}"
                            {{ old('role_id', $usuario->role_id ?? '') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                            @if($rol->descripcion) — {{ $rol->descripcion }} @endif
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-xs mt-1.5 font-semibold" style="color:#ef4444;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="mb-8">
                <label class="block text-sm font-semibold mb-3" style="color:#1c2b16;">Estado</label>
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" id="activo"
                           {{ old('activo', $usuario->activo ?? true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color:#39a900;">
                    <span class="text-sm font-medium" style="color:#5a5a4f;">Usuario activo</span>
                </label>
                <p class="text-xs mt-1.5" style="color:#9a9a8a;">
                    Los usuarios inactivos no pueden iniciar sesión en la plataforma.
                </p>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit"
                        class="px-6 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                        style="background:#39a900;">
                    {{ $editing ? 'Guardar cambios' : 'Crear usuario' }}
                </button>
                <a href="{{ route('superadmin.usuarios.index') }}"
                   class="px-6 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                   style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

    {{-- Info roles --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-bold mb-4" style="color:#1c2b16;">Roles disponibles</h3>
        <div class="space-y-3">
            @foreach($roles as $rol)
            @php
                $rolColors = [
                    1 => ['bg'=>'#f3e8ff','color'=>'#71277a'],
                    2 => ['bg'=>'#f0fdf4','color'=>'#166534'],
                    3 => ['bg'=>'#fffbeb','color'=>'#92400e'],
                ];
                $rc = $rolColors[$rol->id] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];
            @endphp
            <div class="flex items-center gap-3">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold"
                      style="background:{{ $rc['bg'] }}; color:{{ $rc['color'] }};">
                    {{ $rol->nombre }}
                </span>
                @if($rol->descripcion)
                <span class="text-xs" style="color:#5a5a4f;">{{ $rol->descripcion }}</span>
                @endif
                <span class="ml-auto text-xs" style="color:#9a9a8a;">
                    {{ $rol->users()->count() }} usuario{{ $rol->users()->count() != 1 ? 's' : '' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

</div>
</x-superadmin-layout>
