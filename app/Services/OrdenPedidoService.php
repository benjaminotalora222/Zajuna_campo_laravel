<?php

namespace App\Services;

use App\Mail\OrdenPedidoProveedorMail;
use App\Models\Alerta;
use App\Models\LogAuditoria;
use App\Models\NotificacionProveedor;
use App\Models\OrdenPedido;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrdenPedidoService
{
    /**
     * Verifica si el stock de un producto llegó al mínimo y, si es así, genera y envía la orden por correo.
     */
    public static function verificarYGenerarAutomaticamente(Producto $producto, ?int $userId = null): ?OrdenPedido
    {
        $producto->loadMissing('proveedor');
        $stockActual = $producto->stock_calculado;
        $stockMinimo = (int) ($producto->stockMinimo ?? 0);

        // Si no tiene stock mínimo configurado o no ha llegado al mínimo, no aplica
        if ($stockMinimo <= 0 || $stockActual > $stockMinimo) {
            return null;
        }

        // Evitar generar órdenes duplicadas automáticas en menos de 12 horas para el mismo producto
        $ordenReciente = OrdenPedido::where('producto_id', $producto->id)
            ->where('created_at', '>=', now()->subHours(12))
            ->whereIn('estado', ['generada', 'enviada'])
            ->first();

        if ($ordenReciente) {
            return $ordenReciente;
        }

        return static::generarOrdenStockMinimo($producto, null, 'Generada automáticamente al alcanzar stock mínimo', $userId);
    }

    /**
     * Genera y envía una orden de pedido al proveedor de un producto.
     */
    public static function generarOrdenStockMinimo(
        Producto $producto,
        ?int $cantidad = null,
        ?string $observaciones = null,
        ?int $userId = null
    ): ?OrdenPedido {
        $producto->loadMissing('proveedor');
        $proveedor = $producto->proveedor;

        if (!$proveedor) {
            Log::warning("No se puede generar orden de pedido para el producto #{$producto->id} ({$producto->nombre}): sin proveedor asignado.");
            return null;
        }

        $stockActual = $producto->stock_calculado;
        $stockMinimo = (int) ($producto->stockMinimo ?? 0);

        // Si no se especifica cantidad, sugerir reposición al doble del stock mínimo menos el actual
        if (!$cantidad || $cantidad <= 0) {
            $cantidad = max(1, ($stockMinimo * 2) - $stockActual);
        }

        $numeroOrden = OrdenPedido::generarNumeroOrden();
        $correoDestino = trim((string) $proveedor->correo);

        // 1. Crear Orden de Pedido
        $orden = OrdenPedido::create([
            'numero_orden'        => $numeroOrden,
            'proveedor_id'        => $proveedor->id,
            'producto_id'         => $producto->id,
            'stock_actual'        => $stockActual,
            'stock_minimo'        => $stockMinimo,
            'cantidad_solicitada' => $cantidad,
            'estado'              => 'enviada',
            'fecha_generacion'    => now(),
            'fecha_envio'         => !empty($correoDestino) ? now() : null,
            'correo_destino'      => $correoDestino ?: null,
            'observaciones'       => $observaciones ?: 'Reposición urgente por stock mínimo alcanzado.',
            'detalles'            => [[
                'producto_id'   => $producto->id,
                'nombre'        => $producto->nombre,
                'categoria'     => $producto->categoria,
                'unidad'        => $producto->unidad,
                'stock_actual'  => $stockActual,
                'stock_minimo'  => $stockMinimo,
                'cantidad'      => $cantidad,
                'precio'        => $producto->precio,
            ]],
            'user_id'             => $userId ?: auth()->id(),
        ]);

        // 2. Enviar Correo si el proveedor tiene correo registrado
        if (!empty($correoDestino) && filter_var($correoDestino, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($correoDestino)->send(new OrdenPedidoProveedorMail($orden, $proveedor, $producto, $observaciones));

                NotificacionProveedor::create([
                    'orden_pedido_id' => $orden->id,
                    'proveedor_id'    => $proveedor->id,
                    'asunto'          => "Orden de Pedido #{$numeroOrden} - Reposición de {$producto->nombre}",
                    'correo_destino'  => $correoDestino,
                    'mensaje'         => "Se envió orden de pedido por {$cantidad} unidades de {$producto->nombre}.",
                    'estado_envio'    => 'enviado',
                    'fecha_envio'     => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error("Error enviando correo de orden de pedido #{$numeroOrden} a {$correoDestino}: " . $e->getMessage());

                NotificacionProveedor::create([
                    'orden_pedido_id' => $orden->id,
                    'proveedor_id'    => $proveedor->id,
                    'asunto'          => "Orden de Pedido #{$numeroOrden} - Reposición de {$producto->nombre}",
                    'correo_destino'  => $correoDestino,
                    'mensaje'         => "Fallo al enviar correo: " . $e->getMessage(),
                    'estado_envio'    => 'fallido',
                    'fecha_envio'     => now(),
                ]);
            }
        } else {
            Log::info("Orden de pedido #{$numeroOrden} creada sin correo para el proveedor {$proveedor->nombre}.");
        }

        // 3. Registrar Alerta en el sistema
        Alerta::create([
            'tipo'            => 'stock_minimo',
            'mensaje'         => "⚠️ Stock mínimo en {$producto->nombre} ({$stockActual}/{$stockMinimo}). Se generó la orden de pedido #{$numeroOrden} para {$proveedor->nombre}.",
            'fechaGeneracion' => now(),
            'leida'           => false,
            'idProducto'      => $producto->id,
        ]);

        // 4. Registrar en Auditoría
        LogAuditoria::registrar(
            'Inventario',
            'Orden de Pedido',
            "Se generó y envió orden de pedido #{$numeroOrden} ({$cantidad} uds) al proveedor {$proveedor->nombre} ({$correoDestino}) por stock mínimo del producto {$producto->nombre}."
        );

        return $orden;
    }

    /**
     * Revisa todos los productos activos y genera órdenes para aquellos con stock en nivel bajo o agotado.
     */
    public static function revisarTodosProductosStockMinimo(?int $userId = null): array
    {
        $productos = Producto::with(['lotes', 'proveedor'])
            ->where('activo', true)
            ->whereNotNull('stockMinimo')
            ->where('stockMinimo', '>', 0)
            ->get();

        $generadas = [];

        foreach ($productos as $producto) {
            if ($producto->stock_calculado <= $producto->stockMinimo) {
                $orden = static::verificarYGenerarAutomaticamente($producto, $userId);
                if ($orden) {
                    $generadas[] = $orden;
                }
            }
        }

        return $generadas;
    }
}

