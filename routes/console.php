<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
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

// Programación de tareas automáticas (Scheduler)
Schedule::command('zajuna:notificar-pendientes')->dailyAt('08:00');
Schedule::command('zajuna:verificar-stock-minimo')->hourly();
