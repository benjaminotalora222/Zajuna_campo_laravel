<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use App\Services\NotificacionPendientesService;
use App\Services\OrdenPedidoService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('zajuna:notificar-pendientes', function () {
    $this->info('Iniciando envío de notificaciones de tareas y requerimientos pendientes...');
    $resultado = NotificacionPendientesService::notificarTodos();
    $total = $resultado['total_enviados'] ?? 0;
    $this->info("Proceso finalizado. Total de correos enviados: {$total}");
    foreach ($resultado['detalles'] ?? [] as $det) {
        $this->line("  ✓ {$det}");
    }
})->purpose('Envía notificaciones por correo electrónico sobre tareas y requerimientos pendientes');

Artisan::command('zajuna:verificar-stock-minimo', function () {
    $this->info('Verificando productos en nivel de stock mínimo...');
    $ordenes = OrdenPedidoService::revisarTodosProductosStockMinimo();
    $total = count($ordenes);
    $this->info("Proceso finalizado. Órdenes generadas y enviadas: {$total}");
    foreach ($ordenes as $ord) {
        $this->line("  ✓ Orden #{$ord->numero_orden} para {$ord->proveedor?->nombre} ({$ord->producto?->nombre})");
    }
})->purpose('Genera y envía órdenes de pedido por correo electrónico al proveedor cuando el stock llegue al mínimo');

Artisan::command('zajuna:probar-correo {email}', function ($email) {
    $this->info("Enviando correo de prueba a {$email}...");
    try {
        Mail::raw("¡Hola! Este es un correo de prueba de Zajuna Campo para verificar que la configuración de correo SMTP funciona correctamente.\n\nFecha y hora: " . now()->format('d/m/Y H:i:s'), function ($message) use ($email) {
            $message->to($email)->subject('✅ Prueba de correo exitosa · Zajuna Campo');
        });
        $this->info("✓ Correo enviado exitosamente a {$email}. Revisa tu bandeja de entrada (y la carpeta de spam).");
    } catch (\Throwable $e) {
        $this->error("✗ Error al enviar correo: " . $e->getMessage());
    }
})->purpose('Envía un correo de prueba para verificar la configuración SMTP');

Artisan::command('zajuna:enviar-notificacion-orden {email?}', function ($email = null) {
    $orden = \App\Models\OrdenPedido::with(['proveedor', 'producto'])->latest()->first();
    if (!$orden) {
        $this->error("No hay órdenes de pedido registradas.");
        return;
    }
    $destinatario = $email ?: $orden->correo_destino ?: ($orden->proveedor?->correo ?? $orden->proveedor?->email);
    if (!$destinatario) {
        $this->error("No se encontró correo destinatario para la orden #{$orden->numero_orden}.");
        return;
    }
    $this->info("Enviando notificación formal de Orden #{$orden->numero_orden} ({$orden->producto?->nombre}) a {$destinatario}...");
    try {
        Mail::to($destinatario)->send(new \App\Mail\OrdenPedidoProveedorMail($orden, $orden->proveedor, $orden->producto, $orden->observaciones));
        $this->info("✓ Notificación de orden de pedido enviada exitosamente a {$destinatario}.");
    } catch (\Throwable $e) {
        $this->error("✗ Error al enviar: " . $e->getMessage());
    }
})->purpose('Envía la notificación formal de la última orden de pedido al correo especificado');

// Programación de tareas automáticas (Scheduler)
Schedule::command('zajuna:notificar-pendientes')->dailyAt('08:00');
Schedule::command('zajuna:verificar-stock-minimo')->hourly();
