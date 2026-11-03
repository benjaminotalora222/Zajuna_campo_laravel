<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodigoRecuperacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $codigo,
        public string $nombreUsuario
    ) {}

    public function build()
    {
        return $this->subject('Tu código de recuperación de contraseña · Zajuna Campo')
                    ->view('emails.codigo-recuperacion');
    }
}

