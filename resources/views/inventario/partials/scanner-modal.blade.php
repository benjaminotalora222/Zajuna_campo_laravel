@php
    $currentRole = auth()->user()->role_id === 1 ? 'superadmin' : 'operativo';
@endphp

<!-- CDN html5-qrcode para escaneo por cámara de dispositivo -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<div id="modalEscanerInventario" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all" onclick="cerrarModalEscanerAlClickeo(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        
        <!-- ENCABEZADO -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100" style="background:#fcfbf7;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-sm" style="background:#39a900;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 8h10M7 12h10M7 16h10"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-gray-900 leading-tight">Escáner & Salida Rápida</h3>
                    <p class="text-xs text-gray-500">Escanea códigos de barras para registrar salidas o dar de baja vencidos</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalEscaner()" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- CUERPO PRINCIPAL -->
        <div class="p-6 overflow-y-auto space-y-5">

            <!-- BARRA DE ENTRADA / ESCANEO -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    Lector de código de barras / Búsqueda
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </span>
                        <input type="text" id="scannerInputCodigo"
                               placeholder="Escanea con tu lector láser o escribe el código..."
                               autocomplete="off"
                               class="w-full pl-11 pr-4 py-3 rounded-2xl border text-sm font-mono tracking-wide outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100"
                               style="border-color:#e7e0cc; background:#fbfbfb;">
                    </div>
                    <button type="button" onclick="ejecutarBusquedaCodigo()"
                            class="px-5 py-3 rounded-2xl font-bold text-sm text-white transition hover:opacity-95 shadow-sm"
                            style="background:#39a900;">
                        Buscar
                    </button>
                    <button type="button" id="btnToggleCamara" onclick="toggleCamaraEscaner()"
                            class="px-4 py-3 rounded-2xl font-bold text-sm border flex items-center gap-1.5 transition hover:bg-gray-50"
                            style="border-color:#e7e0cc; color:#5a5a4f;" title="Activar cámara web o de celular">
                        <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="hidden sm:inline">Cámara</span>
                    </button>
                </div>
                <p class="text-[11px] text-gray-500 flex items-center gap-1 mt-1">
                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-ping"></span>
                    Listo para pistolas USB/Bluetooth. Presiona Enter o escanea directamente.
                </p>
            </div>

            <!-- VISOR DE CÁMARA (Oculto por defecto) -->
            <div id="scannerCamaraContainer" class="hidden rounded-2xl overflow-hidden border border-purple-200 bg-gray-900 relative">
                <div id="qr-reader" class="w-full" style="min-height: 240px;"></div>
                <div class="absolute top-3 right-3 z-10">
                    <button type="button" onclick="detenerCamaraEscaner()" class="px-3 py-1.5 bg-black/70 hover:bg-black text-white text-xs font-semibold rounded-lg backdrop-blur-sm">
                        Cerrar cámara
                    </button>
                </div>
            </div>

            <!-- MENSAJES Y NOTIFICACIONES -->
            <div id="scannerMensajeError" class="hidden px-4 py-3 rounded-2xl text-xs font-semibold bg-red-50 text-red-700 border border-red-200 flex items-center justify-between">
                <span id="scannerMensajeErrorTexto">Error</span>
                <button type="button" onclick="this.parentElement.classList.add('hidden')" class="text-red-500 hover:text-red-800">✕</button>
            </div>

            <div id="scannerMensajeExito" class="hidden px-4 py-3 rounded-2xl text-xs font-semibold bg-green-50 text-green-700 border border-green-200 flex items-center justify-between">
                <span id="scannerMensajeExitoTexto">Éxito</span>
                <button type="button" onclick="this.parentElement.classList.add('hidden')" class="text-green-500 hover:text-green-800">✕</button>
            </div>

            <!-- SPINNER DE CARGA -->
            <div id="scannerLoading" class="hidden py-8 text-center text-gray-500">
                <svg class="animate-spin h-8 w-8 mx-auto text-green-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <p class="text-xs font-medium">Buscando producto en el sistema...</p>
            </div>

            <!-- TARJETA DEL PRODUCTO IDENTIFICADO -->
            <div id="scannerProductoCard" class="hidden rounded-2xl border border-gray-100 bg-[#faf9f5] p-5 space-y-4">
                
                <!-- Encabezado del producto -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 pb-3 border-b border-gray-200/60">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 id="spNombre" class="text-base font-extrabold text-gray-900 leading-tight">Nombre Producto</h4>
                            <span id="spCategoria" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Categoría</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Proveedor: <span id="spProveedor" class="font-medium text-gray-700">Proveedor</span> ·
                            Código: <span id="spCodigo" class="font-mono font-bold text-gray-800">00000000</span>
                        </p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span id="spPrecio" class="text-base font-extrabold text-gray-900">$0</span>
                        <span id="spUnidad" class="text-xs text-gray-500 font-normal">/ unidad</span>
                    </div>
                </div>

                <!-- Indicadores de stock -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-white rounded-xl p-3 border border-gray-200/60 shadow-2xs">
                        <p class="text-[11px] font-semibold text-gray-500">Stock Disponible Total</p>
                        <p class="text-xl font-extrabold text-green-700 mt-0.5">
                            <span id="spStockTotal">0</span> <span id="spUnidadStock" class="text-xs font-normal text-gray-500">kg</span>
                        </p>
                    </div>
                    <div id="spContenedorVencidos" class="bg-white rounded-xl p-3 border border-red-200 shadow-2xs">
                        <p class="text-[11px] font-semibold text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Stock Vencido
                        </p>
                        <p class="text-xl font-extrabold text-red-600 mt-0.5">
                            <span id="spStockVencido">0</span> <span class="text-xs font-normal text-red-500">vencidos</span>
                        </p>
                    </div>
                </div>

                <!-- ALERTA PRODUCTOS VENCIDOS -->
                <div id="spAlertaVencidos" class="hidden p-3 rounded-xl bg-red-100/70 border border-red-300 text-xs text-red-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⚠️</span>
                        <span>Este producto tiene <strong><span id="spAlertaCantidadVencida">0</span> unidades vencidas</strong> listas para dar de baja.</span>
                    </div>
                    <button type="button" onclick="aplicarBajaRapidaVencidos()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition whitespace-nowrap shadow-xs">
                        Dar de baja vencidos
                    </button>
                </div>

                <!-- FORMULARIO DE SALIDA -->
                <form id="formSalidaRapida" onsubmit="enviarSalidaRapida(event)" class="space-y-4 pt-2">
                    <input type="hidden" id="spProductoId" name="producto_id" value="">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Motivo de salida -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Motivo de salida *</label>
                            <select id="spMotivoSelect" name="motivo" required onchange="manejarCambioMotivo(this.value)"
                                    class="w-full px-3 py-2.5 rounded-xl border text-sm font-medium outline-none transition focus:border-green-600 focus:ring-1 focus:ring-green-100"
                                    style="border-color:#e7e0cc; background:white;">
                                <option value="venta">Venta</option>
                                <option value="vencido">Producto Vencido / Descompuesto</option>
                                <option value="merma">Merma / Avería / Daño</option>
                                <option value="consumo_interno">Consumo Interno</option>
                                <option value="ajuste">Ajuste de inventario</option>
                                <option value="otro">Otro motivo</option>
                            </select>
                        </div>

                        <!-- Lote específico (opcional) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Lote a descargar</label>
                            <select id="spLoteSelect" name="lote_id"
                                    class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none transition focus:border-green-600 focus:ring-1 focus:ring-green-100"
                                    style="border-color:#e7e0cc; background:white;">
                                <option value="">Automático FEFO (lotes vencidos primero)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Cantidad a retirar -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-gray-700">Cantidad a descargar *</label>
                            <span class="text-[11px] text-gray-500">Máx: <strong id="spMaxCantidad">0</strong></span>
                        </div>
                        <div class="flex gap-2">
                            <input type="number" id="spCantidadInput" name="cantidad" min="1" required
                                   onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                                   oninput="this.value = this.value.replace(/\D/g, '')"
                                   class="flex-1 px-3 py-2.5 rounded-xl border text-sm font-extrabold outline-none text-gray-900 transition focus:border-green-600 focus:ring-1 focus:ring-green-100"
                                   style="border-color:#e7e0cc; background:white;" placeholder="1">
                            
                            <!-- Botones rápidos de cantidad -->
                            <button type="button" onclick="sumarCantidadRapida(1)" class="px-3 py-2 rounded-xl text-xs font-bold border hover:bg-gray-100 transition" style="border-color:#e7e0cc;">+1</button>
                            <button type="button" onclick="sumarCantidadRapida(5)" class="px-3 py-2 rounded-xl text-xs font-bold border hover:bg-gray-100 transition" style="border-color:#e7e0cc;">+5</button>
                            <button type="button" onclick="sumarCantidadRapida(10)" class="px-3 py-2 rounded-xl text-xs font-bold border hover:bg-gray-100 transition" style="border-color:#e7e0cc;">+10</button>
                            <button type="button" onclick="asignarTodoStock()" class="px-3 py-2 rounded-xl text-xs font-bold border text-purple-700 hover:bg-purple-50 transition" style="border-color:#d8b4fe;">Todo</button>
                        </div>
                    </div>

                    <!-- Detalle / Observación -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Detalle / Observaciones <span class="font-normal text-gray-400">(opcional)</span></label>
                        <input type="text" id="spMotivoDetalle" name="motivo_detalle"
                               placeholder="Ej: desecho por mal olor, merma en transporte, etc."
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none transition focus:border-green-600 focus:ring-1 focus:ring-green-100"
                               style="border-color:#e7e0cc; background:white;">
                    </div>

                    <!-- Botón de Confirmación -->
                    <div class="pt-2">
                        <button type="submit" id="btnConfirmarSalida"
                                class="w-full py-3.5 rounded-2xl text-sm font-extrabold text-white shadow-md transition hover:opacity-95 flex items-center justify-center gap-2"
                                style="background:#ef4444;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            Confirmar Salida de Inventario
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- PIE DEL MODAL -->
        <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
            <span>Presiona <strong>ESC</strong> para cerrar</span>
            <button type="button" onclick="cerrarModalEscaner()" class="px-4 py-2 font-bold text-gray-600 hover:text-gray-900">
                Cerrar
            </button>
        </div>

    </div>
</div>

<script>
let html5QrScannerInstance = null;
let currentScannedProduct = null;
let hayCambiosInventario = false;
const routePrefixInventario = '{{ $currentRole }}';

// Web Audio API para feedback sonoro inmediato
function emitirSonidoBeep(frecuencia = 880, duracion = 0.12, tipo = 'sine') {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = tipo;
        osc.frequency.setValueAtTime(frecuencia, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + duracion);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + duracion);
    } catch (e) {
        // Silencioso si no hay soporte de audio
    }
}

function emitirSonidoExito() {
    emitirSonidoBeep(587.33, 0.1, 'triangle'); // Re
    setTimeout(() => emitirSonidoBeep(880, 0.2, 'sine'), 100); // La
}

function abrirModalEscaner(codigoInicial = '') {
    const modal = document.getElementById('modalEscanerInventario');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    const input = document.getElementById('scannerInputCodigo');
    input.value = codigoInicial;
    setTimeout(() => input.focus(), 150);

    if (codigoInicial) {
        ejecutarBusquedaCodigo(codigoInicial);
    }
}

function cerrarModalEscaner() {
    detenerCamaraEscaner();
    const modal = document.getElementById('modalEscanerInventario');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // Si hubo salidas exitosas, recargar para ver el inventario actualizado
    if (hayCambiosInventario) {
        window.location.reload();
    }
}

function cerrarModalEscanerAlClickeo(e) {
    if (e.target.id === 'modalEscanerInventario') {
        cerrarModalEscaner();
    }
}

// Búsqueda del producto por código
async function ejecutarBusquedaCodigo(codigoManual = null) {
    const input = document.getElementById('scannerInputCodigo');
    const codigo = (codigoManual !== null ? codigoManual : input.value).trim();

    if (!codigo) {
        mostrarErrorScanner('Ingresa o escanea un código de barras válido.');
        input.focus();
        return;
    }

    ocultarMensajesScanner();
    document.getElementById('scannerLoading').classList.remove('hidden');
    document.getElementById('scannerProductoCard').classList.add('hidden');

    try {
        const resp = await fetch(`/${routePrefixInventario}/inventario/buscar-codigo/${encodeURIComponent(codigo)}`, {
            headers: { 'Accept': 'application/json' }
        });

        const data = await resp.json();
        document.getElementById('scannerLoading').classList.add('hidden');

        if (!resp.ok || !data.success) {
            emitirSonidoBeep(260, 0.25, 'sawtooth'); // Beep de error
            mostrarErrorScanner(data.message || 'Producto no encontrado con el código escaneado.');
            input.select();
            return;
        }

        // Éxito: reproducir sonido y mostrar tarjeta
        emitirSonidoBeep(880, 0.15, 'sine');
        cargarProductoEnUI(data.producto);
    } catch (err) {
        document.getElementById('scannerLoading').classList.add('hidden');
        mostrarErrorScanner('Error de comunicación con el servidor: ' + err.message);
    }
}

function cargarProductoEnUI(prod) {
    currentScannedProduct = prod;

    document.getElementById('spProductoId').value = prod.id;
    document.getElementById('spNombre').innerText = prod.nombre;
    document.getElementById('spCategoria').innerText = prod.categoria || 'General';
    document.getElementById('spProveedor').innerText = prod.proveedor;
    document.getElementById('spCodigo').innerText = prod.codigoBarras || 'ID #' + prod.id;
    document.getElementById('spPrecio').innerText = prod.precio_formateado;
    document.getElementById('spUnidad').innerText = '/ ' + prod.unidad;

    document.getElementById('spStockTotal').innerText = Number(prod.stock_total).toLocaleString();
    document.getElementById('spUnidadStock').innerText = prod.unidad;
    document.getElementById('spStockVencido').innerText = Number(prod.stock_vencido).toLocaleString();
    document.getElementById('spMaxCantidad').innerText = `${prod.stock_total} ${prod.unidad}`;

    // Alerta de vencidos
    const alertaVencidos = document.getElementById('spAlertaVencidos');
    const boxVencidos = document.getElementById('spContenedorVencidos');
    if (prod.stock_vencido > 0) {
        document.getElementById('spAlertaCantidadVencida').innerText = `${prod.stock_vencido} ${prod.unidad}`;
        alertaVencidos.classList.remove('hidden');
        boxVencidos.classList.add('bg-red-50');
    } else {
        alertaVencidos.classList.add('hidden');
        boxVencidos.classList.remove('bg-red-50');
    }

    // Llenar selector de lotes
    const selectLote = document.getElementById('spLoteSelect');
    selectLote.innerHTML = '<option value="">Automático FEFO (lotes vencidos primero)</option>';
    if (prod.lotes && prod.lotes.length > 0) {
        prod.lotes.forEach(l => {
            const opt = document.createElement('option');
            opt.value = l.id;
            let etiqueta = `Lote #${l.id} - ${l.cantidad_disponible} ${prod.unidad} (Vence: ${l.fecha_vencimiento_formateada})`;
            if (l.vencido) etiqueta += ' ⚠️ [VENCIDO]';
            opt.text = etiqueta;
            selectLote.appendChild(opt);
        });
    }

    // Configurar input cantidad
    const inputCant = document.getElementById('spCantidadInput');
    inputCant.max = prod.stock_total;
    inputCant.value = prod.stock_total > 0 ? '1' : '0';

    // Ajustar motivo por defecto: si tiene vencidos, sugerir 'vencido' si stock total = stock vencido
    const selectMotivo = document.getElementById('spMotivoSelect');
    if (prod.stock_vencido > 0 && prod.stock_vencido >= prod.stock_total) {
        selectMotivo.value = 'vencido';
        manejarCambioMotivo('vencido');
    } else {
        selectMotivo.value = 'venta';
        manejarCambioMotivo('venta');
    }

    document.getElementById('scannerProductoCard').classList.remove('hidden');
    inputCant.focus();
    inputCant.select();
}

function manejarCambioMotivo(motivo) {
    const btn = document.getElementById('btnConfirmarSalida');
    const inputCant = document.getElementById('spCantidadInput');
    const detalleInput = document.getElementById('spMotivoDetalle');

    if (motivo === 'vencido') {
        btn.style.background = '#dc2626'; // Rojo más intenso
        btn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Dar de Baja Producto Vencido
        `;
        if (currentScannedProduct && currentScannedProduct.stock_vencido > 0) {
            inputCant.value = currentScannedProduct.stock_vencido;
        }
        if (!detalleInput.value) detalleInput.value = 'Baja por caducidad/vencimiento';
    } else {
        btn.style.background = '#ef4444';
        btn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            Confirmar Salida de Inventario
        `;
        if (detalleInput.value === 'Baja por caducidad/vencimiento') {
            detalleInput.value = '';
        }
    }
}

function aplicarBajaRapidaVencidos() {
    if (!currentScannedProduct) return;
    const selectMotivo = document.getElementById('spMotivoSelect');
    selectMotivo.value = 'vencido';
    manejarCambioMotivo('vencido');
    document.getElementById('spCantidadInput').value = currentScannedProduct.stock_vencido;
    document.getElementById('spMotivoDetalle').value = 'Baja total de lotes vencidos';
    document.getElementById('spCantidadInput').focus();
}

function sumarCantidadRapida(delta) {
    const input = document.getElementById('spCantidadInput');
    let val = parseInt(input.value) || 0;
    val += delta;
    if (currentScannedProduct && val > currentScannedProduct.stock_total) {
        val = currentScannedProduct.stock_total;
    }
    input.value = Math.max(1, val);
}

function asignarTodoStock() {
    if (!currentScannedProduct) return;
    document.getElementById('spCantidadInput').value = currentScannedProduct.stock_total;
}

// Envío de la salida vía AJAX
async function enviarSalidaRapida(e) {
    e.preventDefault();
    ocultarMensajesScanner();

    if (!currentScannedProduct) return;

    const btn = document.getElementById('btnConfirmarSalida');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `
        <svg class="animate-spin h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
        Procesando...
    `;

    const form = document.getElementById('formSalidaRapida');
    const formData = new FormData(form);

    try {
        const resp = await fetch(`/${routePrefixInventario}/inventario/salida-rapida`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await resp.json();
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (!resp.ok || !data.success) {
            emitirSonidoBeep(260, 0.25, 'sawtooth');
            mostrarErrorScanner(data.message || 'Error al procesar la salida.');
            return;
        }

        // Éxito
        emitirSonidoExito();
        hayCambiosInventario = true;
        mostrarExitoScanner(data.message);

        // Actualizar el producto actual
        currentScannedProduct.stock_total = data.nuevo_stock;
        document.getElementById('spStockTotal').innerText = Number(data.nuevo_stock).toLocaleString();
        document.getElementById('spMaxCantidad').innerText = `${data.nuevo_stock} ${currentScannedProduct.unidad}`;
        document.getElementById('spCantidadInput').max = data.nuevo_stock;

        // Limpiar para el siguiente escaneo
        const inputScan = document.getElementById('scannerInputCodigo');
        inputScan.value = '';
        inputScan.focus();

        // Si se quedó en 0 stock
        if (data.nuevo_stock <= 0) {
            document.getElementById('spCantidadInput').value = '0';
        } else {
            document.getElementById('spCantidadInput').value = '1';
        }

    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        mostrarErrorScanner('Error de conexión al guardar la salida: ' + err.message);
    }
}

function mostrarErrorScanner(txt) {
    const errBox = document.getElementById('scannerMensajeError');
    document.getElementById('scannerMensajeErrorTexto').innerText = txt;
    errBox.classList.remove('hidden');
}

function mostrarExitoScanner(txt) {
    const okBox = document.getElementById('scannerMensajeExito');
    document.getElementById('scannerMensajeExitoTexto').innerText = txt;
    okBox.classList.remove('hidden');
}

function ocultarMensajesScanner() {
    document.getElementById('scannerMensajeError').classList.add('hidden');
    document.getElementById('scannerMensajeExito').classList.add('hidden');
}

// ── MANEJO DE CÁMARA (html5-qrcode) ──────────────────────
async function toggleCamaraEscaner() {
    const cont = document.getElementById('scannerCamaraContainer');
    if (cont.classList.contains('hidden')) {
        iniciarCamaraEscaner();
    } else {
        detenerCamaraEscaner();
    }
}

function iniciarCamaraEscaner() {
    if (typeof Html5Qrcode === 'undefined') {
        mostrarErrorScanner('La librería del escáner por cámara no está disponible. Usa el lector USB o teclado.');
        return;
    }

    const cont = document.getElementById('scannerCamaraContainer');
    cont.classList.remove('hidden');

    try {
        html5QrScannerInstance = new Html5Qrcode("qr-reader");
        const config = {
            fps: 10,
            qrbox: { width: 260, height: 160 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.QR_CODE
            ]
        };

        html5QrScannerInstance.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                emitirSonidoBeep(920, 0.15, 'sine');
                document.getElementById('scannerInputCodigo').value = decodedText;
                detenerCamaraEscaner();
                ejecutarBusquedaCodigo(decodedText);
            },
            (errorMsg) => {
                // Errores normales de lectura de frame ignorados
            }
        ).catch(err => {
            detenerCamaraEscaner();
            mostrarErrorScanner('No se pudo acceder a la cámara: ' + (err.name || err.message || err));
        });
    } catch (e) {
        detenerCamaraEscaner();
        mostrarErrorScanner('Error al inicializar cámara: ' + e.message);
    }
}

function detenerCamaraEscaner() {
    const cont = document.getElementById('scannerCamaraContainer');
    cont.classList.add('hidden');

    if (html5QrScannerInstance) {
        try {
            html5QrScannerInstance.stop().then(() => {
                html5QrScannerInstance.clear();
                html5QrScannerInstance = null;
            }).catch(() => {
                html5QrScannerInstance = null;
            });
        } catch(e) {
            html5QrScannerInstance = null;
        }
    }
}

// Escuchar teclas en el campo de código (detección automática de pistolas USB que dan Enter)
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('scannerInputCodigo');
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                ejecutarBusquedaCodigo();
            }
        });
    }

    // Tecla ESC cierra el modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('modalEscanerInventario');
            if (modal && !modal.classList.contains('hidden')) {
                cerrarModalEscaner();
            }
        }
    });
});
</script>

