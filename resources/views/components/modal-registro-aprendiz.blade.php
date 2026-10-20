@props(['proyectos' => collect(), 'route' => route('superadmin.proyectos.aprendices.store')])

{{-- ══ MODAL REGISTRO DE APRENDICES ════════════════════════════ --}}
<div id="modalAprendiz" class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-5" style="background:rgba(0,0,0,0.55); backdrop-filter: blur(2px);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl max-h-[92vh] flex flex-col overflow-hidden border border-gray-100" onclick="event.stopPropagation()">
        
        {{-- Header Sticky --}}
        <div class="flex items-center justify-between px-6 sm:px-8 py-4 border-b sticky top-0 bg-white z-20" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-sm shrink-0" style="background:#39a900;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M19 8v6M22 11h-6"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-extrabold" style="color:#1c2b16;">Registro de Aprendices</h2>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full" style="background:#fdf9ee; color:#71277a; border:1px solid #e7e0cc;">Investigación SENA</span>
                    </div>
                    <p class="text-xs text-gray-500">Formulario oficial de vinculación a semilleros y proyectos.</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalAprendiz()" class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Body Scrollable --}}
        <form id="formAprendiz" method="POST" action="{{ $route }}" class="overflow-y-auto px-6 sm:px-8 py-6 space-y-6 flex-1">
            @csrf

            @if($errors->hasAny(['nombre_completo', 'tipo_documento', 'numero_documento', 'genero', 'rh', 'eps', 'telefono', 'correo_electronico', 'tipo_programa', 'programa_formacion', 'ficha_programa', 'semillero', 'nombre_proyecto', 'instructor_lider_asesores']))
            <div class="p-4 rounded-2xl text-xs space-y-1.5" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <div class="font-bold flex items-center gap-1.5 text-sm">
                    <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Por favor revisa los errores señalados en el formulario:
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ══════════════════════════════════════════════════
                 SECCIÓN 1: DATOS PERSONALES
                 ══════════════════════════════════════════════════ --}}
            <div class="bg-gray-50/70 rounded-2xl p-4 sm:p-5 border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background:#71277a;">1</span>
                    <h3 class="text-xs font-black uppercase tracking-wider" style="color:#71277a;">Información Personal del Aprendiz</h3>
                </div>

                {{-- 1. NOMBRE COMPLETO --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                            1. NOMBRE COMPLETO <span class="text-red-500 font-black">*</span>
                        </label>
                        <span class="text-[10.5px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            Se convierte automáticamente a MAYÚSCULAS mientras se escribe
                        </span>
                    </div>
                    <input type="text"
                           name="nombre_completo"
                           id="inpAprendizNombreCompleto"
                           required
                           value="{{ old('nombre_completo') }}"
                           placeholder="EJ: JUAN CARLOS PÉREZ GÓMEZ"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm font-bold outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100 bg-white"
                           style="border-color:#e7e0cc; text-transform: uppercase;"
                           oninput="this.value = this.value.toUpperCase()">
                    @error('nombre_completo')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 2. TIPO DE DOCUMENTO --}}
                <div>
                    <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                        2. TIPO DE DOCUMENTO <span class="text-red-500 font-black">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        @foreach(['T.I' => 'Tarjeta de Identidad', 'CC' => 'Cédula de Ciudadanía', 'OTRO' => 'Otro Tipo'] as $tdVal => $tdLabel)
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_documento" value="{{ $tdVal }}" required
                                   {{ old('tipo_documento', 'CC') === $tdVal ? 'checked' : '' }}
                                   onchange="toggleTipoDocOtro(this.value)"
                                   class="peer hidden">
                            <div class="px-3 py-2.5 rounded-xl border text-center transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50/60 peer-checked:shadow-sm hover:border-gray-300 bg-white"
                                 style="border-color:#e7e0cc;">
                                <div class="font-black text-xs peer-checked:text-emerald-800" style="color:#1c2b16;">{{ $tdVal }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $tdLabel }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    <div id="wrapTipoDocOtro" class="{{ old('tipo_documento') === 'OTRO' ? '' : 'hidden' }} mt-2">
                        <input type="text" name="tipo_documento_otro" id="inpTipoDocOtro"
                               value="{{ old('tipo_documento_otro') }}"
                               placeholder="Especifique el tipo de documento (Ej: Cédula de Extranjería, PPT, etc.)..."
                               class="w-full px-3.5 py-2 rounded-xl border text-xs outline-none bg-white font-medium"
                               style="border-color:#e7e0cc;">
                    </div>
                    @error('tipo_documento')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- 3. NÚMERO DE DOCUMENTO --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                                3. NÚMERO DE DOCUMENTO (sin puntos ni comas) <span class="text-red-500 font-black">*</span>
                            </label>
                            <span class="text-[10px] font-semibold text-gray-400">Solo números</span>
                        </div>
                        <input type="text"
                               name="numero_documento"
                               id="inpNumeroDocumento"
                               required
                               inputmode="numeric"
                               pattern="[0-9]*"
                               value="{{ old('numero_documento') }}"
                               placeholder="Ej: 1098765432"
                               class="w-full px-4 py-2.5 rounded-xl border text-sm font-bold outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100 bg-white"
                               style="border-color:#e7e0cc;"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               onkeydown="return !['.', ',', 'e', 'E', '+', '-'].includes(event.key)"
                               onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                        @error('numero_documento')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    {{-- 4. GÉNERO --}}
                    <div>
                        <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                            4. GÉNERO <span class="text-red-500 font-black">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach(['FEMENINO', 'MASCULINO', 'OTRO'] as $gen)
                            <label class="cursor-pointer">
                                <input type="radio" name="genero" value="{{ $gen }}" required
                                       {{ old('genero') === $gen ? 'checked' : '' }}
                                       class="peer hidden">
                                <div class="px-2 py-2.5 rounded-xl border text-center transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50/60 peer-checked:shadow-sm hover:border-gray-300 bg-white"
                                     style="border-color:#e7e0cc;">
                                    <span class="font-extrabold text-xs peer-checked:text-emerald-800" style="color:#1c2b16;">{{ $gen }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('genero')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- 5. RH --}}
                    <div>
                        <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                            5. RH <span class="text-red-500 font-black">*</span>
                        </label>
                        <select name="rh" required
                                class="w-full px-3 py-2.5 rounded-xl border text-sm font-bold outline-none transition focus:border-green-600 bg-white cursor-pointer"
                                style="border-color:#e7e0cc;">
                            <option value="" disabled {{ old('rh') ? '' : 'selected' }}>Seleccione RH...</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $rhOpt)
                                <option value="{{ $rhOpt }}" {{ old('rh') === $rhOpt ? 'selected' : '' }}>{{ $rhOpt }}</option>
                            @endforeach
                        </select>
                        @error('rh')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    {{-- 6. EPS --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                            6. EPS <span class="text-red-500 font-black">*</span>
                        </label>
                        <input type="text"
                               name="eps"
                               required
                               value="{{ old('eps') }}"
                               placeholder="Ej: Sanitas, Sura, Nueva EPS, Famisanar..."
                               class="w-full px-4 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-green-600 bg-white"
                               style="border-color:#e7e0cc;">
                        @error('eps')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- 7. TELÉFONO --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                                7. TELÉFONO <span class="text-red-500 font-black">*</span>
                            </label>
                            <span id="telCountIndicator" class="text-[10px] font-bold text-gray-400">Solo números, 10 dígitos</span>
                        </div>
                        <input type="tel"
                               name="telefono"
                               id="inpAprendizTelefono"
                               required
                               maxlength="10"
                               minlength="10"
                               inputmode="numeric"
                               pattern="[0-9]{10}"
                               value="{{ old('telefono') }}"
                               placeholder="Ej: 3101234567"
                               class="w-full px-4 py-2.5 rounded-xl border text-sm font-bold outline-none transition focus:border-green-600 bg-white"
                               style="border-color:#e7e0cc;"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10); document.getElementById('telCountIndicator').textContent = this.value.length + '/10 dígitos';"
                               onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                        @error('telefono')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    {{-- 8. CORREO ELECTRÓNICO --}}
                    <div>
                        <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                            8. CORREO ELECTRÓNICO <span class="text-red-500 font-black">*</span>
                        </label>
                        <input type="email"
                               name="correo_electronico"
                               id="inpAprendizCorreo"
                               required
                               value="{{ old('correo_electronico') }}"
                               placeholder="Ej: aprendiz@soy.sena.edu.co"
                               class="w-full px-4 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-green-600 bg-white"
                               style="border-color:#e7e0cc;">
                        @error('correo_electronico')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 SECCIÓN 2: FORMACIÓN ACADÉMICA Y APOYOS SENA
                 ══════════════════════════════════════════════════ --}}
            <div class="bg-gray-50/70 rounded-2xl p-4 sm:p-5 border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background:#71277a;">2</span>
                    <h3 class="text-xs font-black uppercase tracking-wider" style="color:#71277a;">Programa de Formación SENA</h3>
                </div>

                {{-- 9. TIPO DE PROGRAMA --}}
                <div>
                    <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                        9. TIPO DE PROGRAMA <span class="text-red-500 font-black">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2.5">
                        @foreach(['Tecnólogo', 'Técnico', 'Complementaria'] as $tp)
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo_programa" value="{{ $tp }}" required
                                   {{ old('tipo_programa', 'Tecnólogo') === $tp ? 'checked' : '' }}
                                   class="peer hidden">
                            <div class="px-3 py-2.5 rounded-xl border text-center transition peer-checked:border-emerald-600 peer-checked:bg-emerald-50/60 peer-checked:shadow-sm hover:border-gray-300 bg-white"
                                 style="border-color:#e7e0cc;">
                                <span class="font-black text-xs peer-checked:text-emerald-800" style="color:#1c2b16;">{{ $tp }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('tipo_programa')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 10. PROGRAMA DE FORMACIÓN --}}
                <div>
                    <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                        10. PROGRAMA DE FORMACIÓN <span class="text-red-500 font-black">*</span>
                    </label>
                    @php
                    $programasSena = [
                        'GESTIÓN AGROEMPRESARIAL',
                        'ACUICULTURA',
                        'GESTIÓN DE LA PRODUCCIÓN GANADERA',
                        'AGROBIOTECNOLOGÍA',
                        'GESTIÓN INTEGRADA A LA CALIDAD, MEDIO AMBIENTAL, SEGURIDAD Y SALUD OCUPACIONAL',
                        'GESTIÓN DE LA PRODUCCIÓN AGRÍCOLA',
                        'MECANIZACIÓN AGRÍCOLA',
                        'GESTIÓN DE MERCADOS',
                        'PREVENCIÓN Y CONTROL AMBIENTAL',
                        'ANÁLISIS Y DESARROLLO DE SOFTWARE',
                        'ANÁLISIS Y DESARROLLO DE SISTEMAS DE INFORMACIÓN',
                        'GESTIÓN DE RECURSOS NATURALES',
                        'PRODUCCIÓN AGROPECUARIA ECOLÓGICA',
                        'PROCESAMIENTO DE ALIMENTOS',
                        'ALISTAMIENTO DE LABORATORIOS DE MICROBIOLOGÍA Y BIOTECNOLOGÍA',
                        'PRODUCCIÓN PECUARIA',
                        'CHOCOLATERÍA',
                        'OTRO',
                    ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1 border rounded-xl p-2.5 bg-white" style="border-color:#e7e0cc;">
                        @foreach($programasSena as $pItem)
                        <label class="cursor-pointer flex items-start gap-2.5 p-2 rounded-lg border border-transparent hover:bg-emerald-50/30 transition has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-400">
                            <input type="radio" name="programa_formacion" value="{{ $pItem }}" required
                                   {{ old('programa_formacion') === $pItem ? 'checked' : '' }}
                                   onchange="toggleProgramaOtro(this.value)"
                                   class="mt-0.5 text-emerald-600 focus:ring-emerald-500 shrink-0">
                            <span class="text-xs leading-snug {{ $pItem === 'OTRO' ? 'font-black text-purple-700' : 'font-semibold text-gray-800' }}">
                                {{ $pItem }}
                            </span>
                        </label>
                        @endforeach
                    </div>

                    <div id="wrapProgramaOtro" class="{{ old('programa_formacion') === 'OTRO' ? '' : 'hidden' }} mt-2">
                        <label class="block text-[11px] font-bold text-purple-900 mb-1">Escriba el nombre del programa de formación:</label>
                        <input type="text" name="programa_formacion_otro" id="inpProgramaOtro"
                               value="{{ old('programa_formacion_otro') }}"
                               placeholder="Ej: PRODUCCIÓN DE ESPECIES MENORES..."
                               class="w-full px-4 py-2.5 rounded-xl border text-xs font-semibold outline-none bg-purple-50/40 border-purple-300 focus:border-purple-600">
                    </div>
                    @error('programa_formacion')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 11. FICHA DEL PROGRAMA DE FORMACIÓN --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                            11. FICHA DEL PROGRAMA DE FORMACIÓN <span class="text-red-500 font-black">*</span>
                        </label>
                        <span class="text-[10px] font-semibold text-gray-400">Solo números</span>
                    </div>
                    <input type="text"
                           name="ficha_programa"
                           required
                           inputmode="numeric"
                           pattern="[0-9]*"
                           value="{{ old('ficha_programa') }}"
                           placeholder="Ej: 2874612"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm font-bold outline-none transition focus:border-green-600 bg-white"
                           style="border-color:#e7e0cc;"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                    @error('ficha_programa')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 12. ¿Cuenta con tipo de auxilio o de apoyo económico por parte del SENA? --}}
                <div>
                    <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                        12. ¿Cuenta con tipo de auxilio o de apoyo económico por parte del SENA?
                    </label>
                    @php
                    $apoyosSena = [
                        'APOYO Y SOSTENIMIENTO',
                        'CONTRATO DE APRENDIZAJE ETAPA LECTIVA',
                        'CENTRO DE CONVIVENCIA',
                        'APOYO ALIMENTACIÓN',
                        'OTRO',
                        'NINGUNO',
                    ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($apoyosSena as $apItem)
                        <label class="cursor-pointer flex items-center gap-2.5 p-2.5 rounded-xl border transition has-[:checked]:bg-emerald-50/60 has-[:checked]:border-emerald-500 bg-white"
                               style="border-color:#e7e0cc;">
                            <input type="radio" name="apoyo_sena" value="{{ $apItem }}"
                                   {{ old('apoyo_sena', 'NINGUNO') === $apItem ? 'checked' : '' }}
                                   onchange="toggleApoyoOtro(this.value)"
                                   class="text-emerald-600 focus:ring-emerald-500 shrink-0">
                            <span class="text-xs font-bold {{ $apItem === 'OTRO' ? 'text-purple-700 font-black' : 'text-gray-800' }}">
                                {{ $apItem }}
                            </span>
                        </label>
                        @endforeach
                    </div>

                    <div id="wrapApoyoOtro" class="{{ old('apoyo_sena') === 'OTRO' ? '' : 'hidden' }} mt-2">
                        <label class="block text-[11px] font-bold text-purple-900 mb-1">Especifique cuál auxilio o apoyo económico:</label>
                        <input type="text" name="apoyo_sena_otro" id="inpApoyoOtro"
                               value="{{ old('apoyo_sena_otro') }}"
                               placeholder="Escriba aquí el detalle del auxilio o apoyo económico..."
                               class="w-full px-4 py-2.5 rounded-xl border text-xs font-semibold outline-none bg-purple-50/40 border-purple-300 focus:border-purple-600">
                    </div>
                    @error('apoyo_sena')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════
                 SECCIÓN 3: INVESTIGACIÓN Y PROYECTO
                 ══════════════════════════════════════════════════ --}}
            <div class="bg-gray-50/70 rounded-2xl p-4 sm:p-5 border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                    <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-black text-white" style="background:#71277a;">3</span>
                    <h3 class="text-xs font-black uppercase tracking-wider" style="color:#71277a;">Investigación, Semillero y Proyecto</h3>
                </div>

                {{-- 13. ¿A QUÉ SEMILLERO DE INVESTIGACIÓN DESEA VINCULARSE? --}}
                <div>
                    <label class="block text-xs font-black uppercase mb-1.5" style="color:#1c2b16;">
                        13. ¿A QUÉ SEMILLERO DE INVESTIGACIÓN DESEA VINCULARSE? <span class="text-red-500 font-black">*</span>
                    </label>
                    @php
                    $semillerosList = [
                        'SIPPA',
                        'SEBI',
                        'INNOVACIÓN AGROINDUSTRIAL',
                        'FISENSO',
                        'AGRÍCOLA',
                        'AGROGESTIÓN',
                        'IDEAR',
                        'TEDAF',
                    ];
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($semillerosList as $sem)
                        <label class="cursor-pointer">
                            <input type="radio" name="semillero" value="{{ $sem }}" required
                                   {{ old('semillero') === $sem ? 'checked' : '' }}
                                   class="peer hidden">
                            <div class="p-2.5 rounded-xl border text-center transition peer-checked:border-purple-600 peer-checked:bg-purple-100/70 peer-checked:shadow-sm hover:border-gray-300 bg-white"
                                 style="border-color:#e7e0cc;">
                                <div class="font-black text-xs peer-checked:text-purple-900" style="color:#71277a;">{{ $sem }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('semillero')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 14. NOMBRE DEL PROYECTO DE INVESTIGACIÓN EN EL QUE ESTÁ TRABAJANDO --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                            14. NOMBRE DEL PROYECTO DE INVESTIGACIÓN EN EL QUE ESTÁ TRABAJANDO <span class="text-red-500 font-black">*</span>
                        </label>
                        <span class="text-[10px] font-semibold text-gray-400">Texto largo (párrafo)</span>
                    </div>
                    <textarea name="nombre_proyecto"
                              id="inpNombreProyecto"
                              rows="3"
                              required
                              placeholder="Describa el nombre completo del proyecto de investigación en el que está trabajando..."
                              class="w-full px-4 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-green-600 bg-white resize-y"
                              style="border-color:#e7e0cc;">{{ old('nombre_proyecto') }}</textarea>

                    {{-- Sugerencias de proyectos registrados en el sistema --}}
                    @if(isset($proyectos) && $proyectos->count())
                    <div class="mt-1.5 flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10.5px] text-gray-400 font-bold">Proyectos existentes:</span>
                        @foreach($proyectos->take(4) as $prSugg)
                            <button type="button" onclick="document.getElementById('inpNombreProyecto').value = '{{ addslashes($prSugg->nombre) }}'"
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md hover:bg-purple-100 transition truncate max-w-[220px]"
                                    style="background:#f3e8ff; color:#71277a;" title="Haga clic para autocompletar">
                                {{ $prSugg->nombre }}
                            </button>
                        @endforeach
                    </div>
                    @endif
                    @error('nombre_proyecto')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>

                {{-- 15. NOMBRE DEL INSTRUCTOR LÍDER Y ASESORES DEL PROYECTO DE INVESTIGACIÓN --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase" style="color:#1c2b16;">
                            15. NOMBRE DEL INSTRUCTOR LÍDER Y ASESORES DEL PROYECTO DE INVESTIGACIÓN <span class="text-red-500 font-black">*</span>
                        </label>
                        <span class="text-[10px] font-semibold text-gray-400">Texto largo (párrafo)</span>
                    </div>
                    <textarea name="instructor_lider_asesores"
                              id="inpInstructorLider"
                              rows="3"
                              required
                              placeholder="Escriba los nombres completos de los instructores líderes y asesores que orientan el proyecto..."
                              class="w-full px-4 py-2.5 rounded-xl border text-sm font-semibold outline-none transition focus:border-green-600 bg-white resize-y"
                              style="border-color:#e7e0cc;">{{ old('instructor_lider_asesores') }}</textarea>
                    @error('instructor_lider_asesores')<p class="text-[11px] text-red-500 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Footer Sticky Buttons --}}
            <div class="flex items-center gap-3 pt-3 border-t sticky bottom-0 bg-white -mx-6 sm:-mx-8 px-6 sm:px-8 py-3 z-10" style="border-color:#f3f0e8;">
                <button type="submit"
                        class="flex-1 py-3 px-6 rounded-xl font-black text-sm text-white transition hover:opacity-95 shadow-md flex items-center justify-center gap-2"
                        style="background:#39a900;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Guardar Registro de Aprendiz
                </button>
                <button type="button" onclick="cerrarModalAprendiz()"
                        class="px-6 py-3 rounded-xl font-bold text-sm border hover:bg-gray-100 transition"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalAprendiz() {
    const modal = document.getElementById('modalAprendiz');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModalAprendiz() {
    const modal = document.getElementById('modalAprendiz');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function toggleTipoDocOtro(val) {
    const wrap = document.getElementById('wrapTipoDocOtro');
    const inp  = document.getElementById('inpTipoDocOtro');
    if (val === 'OTRO') {
        wrap.classList.remove('hidden');
        inp.focus();
    } else {
        wrap.classList.add('hidden');
    }
}

function toggleProgramaOtro(val) {
    const wrap = document.getElementById('wrapProgramaOtro');
    const inp  = document.getElementById('inpProgramaOtro');
    if (val === 'OTRO') {
        wrap.classList.remove('hidden');
        inp.required = true;
        inp.focus();
    } else {
        wrap.classList.add('hidden');
        inp.required = false;
    }
}

function toggleApoyoOtro(val) {
    const wrap = document.getElementById('wrapApoyoOtro');
    const inp  = document.getElementById('inpApoyoOtro');
    if (val === 'OTRO') {
        wrap.classList.remove('hidden');
        inp.required = true;
        inp.focus();
    } else {
        wrap.classList.add('hidden');
        inp.required = false;
    }
}

document.getElementById('modalAprendiz').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalAprendiz();
});

@if($errors->hasAny(['nombre_completo', 'tipo_documento', 'numero_documento', 'genero', 'rh', 'eps', 'telefono', 'correo_electronico', 'tipo_programa', 'programa_formacion', 'programa_formacion_otro', 'ficha_programa', 'apoyo_sena', 'apoyo_sena_otro', 'semillero', 'nombre_proyecto', 'instructor_lider_asesores']))
    document.addEventListener('DOMContentLoaded', () => abrirModalAprendiz());
@endif
</script>
