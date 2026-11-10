<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TareasRequerimientosPendientesMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $destinatario,
        public Collection $tareas,
        public Collection $actividades,
        public bool $esConsolidado = false
    ) {}

    public function build()
    {
        $conteoTareas = $this->tareas->count();
        $conteoRequerimientos = $this->actividades->count();
        $total = $conteoTareas + $conteoRequerimientos;

        $titulo = $this->esConsolidado
            ? "🔔 Resumen Global: {$total} Tareas y Requerimientos Pendientes · Zajuna Campo"
            : "📋 Tienes {$total} Tareas y Requerimientos Pendientes · Zajuna Campo";

        return $this->subject($titulo)
                    ->view('emails.tareas-requerimientos-pendientes');
    }
}

