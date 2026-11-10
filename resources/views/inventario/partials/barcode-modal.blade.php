@php
    $currentRole = auth()->user()->role_id === 1 ? 'superadmin' : 'operativo';
@endphp

<!-- CDN JsBarcode para renderizado de alta precisión de códigos de barras -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<div id="modalCodigoBarras" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all" onclick="cerrarModalCodigoBarras(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 animate-in fade-in zoom-in-95 duration-200" onclick="event.stopPropagation()">
        
        <!-- ENCABEZADO -->
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100" style="background:#fcfbf7;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-sm" style="background:#71277a;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h10M3 4h18M3 20h18"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-lg text-gray-900 leading-tight">Etiqueta de Código de Barras</h3>
                    <p class="text-xs text-gray-500">Visualiza, genera e imprime etiquetas adhesivas</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCodigoBarras()" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- CONTENIDO / VISTA PREVIA DE ETIQUETA -->
        <div class="p-6 space-y-6">
            
            <!-- CONTENEDOR SI NO TIENE CÓDIGO -->
            <div id="cbSinCodigoBox" class="hidden text-center py-6 space-y-3">
                <div class="w-14 h-14 mx-auto rounded-full bg-purple-50 flex items-center justify-center text-purple-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h4 class="font-extrabold text-gray-800">Este producto aún no tiene código de barras</h4>
                <p class="text-xs text-gray-500 max-w-xs mx-auto">
                    Genera automáticamente un código único EAN-13 para poder imprimir etiquetas y escanear en inventario.
                </p>
                <div class="pt-2">
                    <button type="button" id="btnGenerarCodigoBarrasModal" onclick="generarCodigoBarrasDesdeModal()"
                            class="px-6 py-3 rounded-2xl font-bold text-sm text-white shadow-md transition hover:opacity-95"
                            style="background:#71277a;">
                        + Generar Código de Barras
                    </button>
                </div>
            </div>

            <!-- ETIQUETA IMPRIMIBLE (TIPO STICKER ADHESIVO) -->
            <div id="cbConCodigoBox" class="hidden">
                <div id="etiquetaImprimibleSticker" class="border-2 border-dashed border-gray-300 rounded-2xl p-5 bg-white shadow-xs text-center mx-auto max-w-xs space-y-2">
                    <!-- Marca -->
                    <div class="flex items-center justify-between border-b border-gray-200 pb-1.5">
                        <span class="text-[11px] font-black tracking-wider text-green-700 uppercase">ZAJUNA CAMPO</span>
                        <span id="cbEtiquetaCategoria" class="text-[10px] font-bold text-purple-700 uppercase bg-purple-50 px-1.5 py-0.5 rounded">Verduras</span>
                    </div>

                    <!-- Nombre del producto -->
                    <h4 id="cbEtiquetaNombre" class="font-extrabold text-base text-gray-900 leading-tight pt-1">Nombre Producto</h4>
                    <p id="cbEtiquetaProveedor" class="text-[11px] text-gray-500 -mt-1 truncate">Proveedor</p>

                    <!-- SVG del Código de Barras -->
                    <div class="py-1 flex justify-center overflow-hidden">
                        <svg id="svgCodigoBarras" class="max-w-full"></svg>
                    </div>

                    <!-- Precio y Unidad -->
                    <div class="pt-1 border-t border-gray-200 flex items-center justify-between">
                        <span class="text-[10px] text-gray-400 uppercase font-semibold">Precio / <span id="cbEtiquetaUnidad">kg</span></span>
                        <span id="cbEtiquetaPrecio" class="text-base font-black text-gray-900">$0</span>
                    </div>
                </div>

                <!-- CONTROLES DE ACCIÓN -->
                <div class="grid grid-cols-2 gap-3 pt-5">
                    <button type="button" onclick="imprimirEtiquetaSticker()"
                            class="w-full py-3 rounded-2xl font-bold text-sm text-white shadow-md flex items-center justify-center gap-2 hover:opacity-95 transition"
                            style="background:#39a900;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimir Etiqueta
                    </button>

                    <button type="button" onclick="descargarSvgCodigoBarras()"
                            class="w-full py-3 rounded-2xl font-bold text-sm border flex items-center justify-center gap-2 hover:bg-gray-50 transition"
                            style="border-color:#e7e0cc; color:#5a5a4f;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- ESTILOS DE IMPRESIÓN PARA ETIQUETAS ADHESIVAS TÉRMICAS / ESTÁNDAR -->
<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #etiquetaImprimibleSticker, #etiquetaImprimibleSticker * {
        visibility: visible !important;
    }
    #etiquetaImprimibleSticker {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 60mm !important;
        max-width: 60mm !important;
        padding: 4mm !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
let productoSeleccionadoCB = null;
const rolePrefixCB = '{{ $currentRole }}';

function abrirModalCodigoBarras(prod) {
    productoSeleccionadoCB = prod;
    const modal = document.getElementById('modalCodigoBarras');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    actualizarVistaModalCB();
}

function cerrarModalCodigoBarras(e = null) {
    if (e && e.target.id !== 'modalCodigoBarras') return;
    const modal = document.getElementById('modalCodigoBarras');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function actualizarVistaModalCB() {
    if (!productoSeleccionadoCB) return;

    const p = productoSeleccionadoCB;
    const tieneCodigo = !!(p.codigoBarras && p.codigoBarras.trim() !== '');

    const sinCodigoBox = document.getElementById('cbSinCodigoBox');
    const conCodigoBox = document.getElementById('cbConCodigoBox');

    if (!tieneCodigo) {
        sinCodigoBox.classList.remove('hidden');
        conCodigoBox.classList.add('hidden');
    } else {
        sinCodigoBox.classList.add('hidden');
        conCodigoBox.classList.remove('hidden');

        // Llenar campos de la etiqueta
        document.getElementById('cbEtiquetaNombre').innerText = p.nombre;
        document.getElementById('cbEtiquetaCategoria').innerText = p.categoria || 'General';
        document.getElementById('cbEtiquetaProveedor').innerText = p.proveedor || '';
        document.getElementById('cbEtiquetaUnidad').innerText = p.unidad || 'und';
        document.getElementById('cbEtiquetaPrecio').innerText = p.precio_formateado || ('$' + Number(p.precio || 0).toLocaleString());

        // Renderizar el código de barras en el SVG
        renderizarCodigoBarrasSvg(p.codigoBarras);
    }
}

function renderizarCodigoBarrasSvg(codigo) {
    const svgElem = document.getElementById('svgCodigoBarras');
    svgElem.innerHTML = '';

    if (typeof JsBarcode !== 'undefined') {
        try {
            // Intentar renderizar en CODE128 o EAN13 según el largo
            JsBarcode(svgElem, String(codigo), {
                format: "CODE128",
                width: 1.8,
                height: 52,
                displayValue: true,
                fontSize: 13,
                font: "monospace",
                textAlign: "center",
                textPosition: "bottom",
                textMargin: 3,
                margin: 4,
                background: "#ffffff",
                lineColor: "#000000"
            });
            return;
        } catch (e) {
            console.warn('JsBarcode fallback:', e);
        }
    }

    // Fallback puro SVG si no se cargó JsBarcode
    svgElem.setAttribute('viewBox', '0 0 200 60');
    svgElem.setAttribute('width', '100%');
    svgElem.setAttribute('height', '50');
    svgElem.innerHTML = `
        <rect width="100%" height="100%" fill="#fff"/>
        <text x="50%" y="28" font-family="monospace" font-size="14" font-weight="bold" text-anchor="middle" fill="#000">${codigo}</text>
        <text x="50%" y="45" font-family="sans-serif" font-size="10" text-anchor="middle" fill="#666">|||| ||||| |||| ||||</text>
    `;
}

async function generarCodigoBarrasDesdeModal() {
    if (!productoSeleccionadoCB) return;

    const btn = document.getElementById('btnGenerarCodigoBarrasModal');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Generando...';

    try {
        const resp = await fetch(`/${rolePrefixCB}/inventario/${productoSeleccionadoCB.id}/generar-codigo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await resp.json();
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (data.success && data.codigoBarras) {
            productoSeleccionadoCB.codigoBarras = data.codigoBarras;
            actualizarVistaModalCB();
            // Marcar para recargar la página al cerrar
            hayCambiosInventario = true;
        } else {
            alert(data.message || 'No se pudo generar el código.');
        }
    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('Error al comunicarse con el servidor: ' + err.message);
    }
}

function imprimirEtiquetaSticker() {
    window.print();
}

function descargarSvgCodigoBarras() {
    const svgElem = document.getElementById('svgCodigoBarras');
    if (!svgElem) return;

    const serializer = new XMLSerializer();
    let source = serializer.serializeToString(svgElem);

    // Asegurar namespaces
    if(!source.match(/^<svg[^>]+xmlns="http\:\/\/www\.w3\.org\/2000\/svg"/)){
        source = source.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
    }

    const blob = new Blob([source], {type: "image/svg+xml;charset=utf-8"});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `codigo_${productoSeleccionadoCB.codigoBarras || productoSeleccionadoCB.id}.svg`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

