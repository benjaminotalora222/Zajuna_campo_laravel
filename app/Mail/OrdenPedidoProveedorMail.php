<?php

namespace App\Mail;

use App\Models\OrdenPedido;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdenPedidoProveedorMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public OrdenPedido $ordenPedido,
        public Proveedor $proveedor,
        public ?Producto $producto = null,
        public ?string $mensajeAdicional = null
    ) {}

    public function build()
    {
        $numero = $this->ordenPedido->numero_orden ?? 'ORD-' . $this->ordenPedido->id;
        $prodNombre = $this->producto ? " - {$this->producto->nombre}" : '';

        return $this->subject("🛒 Orden de Pedido #{$numero}{$prodNombre} · Reabastecimiento Zajuna Campo")
                    ->view('emails.orden-pedido-proveedor');
    }
}

