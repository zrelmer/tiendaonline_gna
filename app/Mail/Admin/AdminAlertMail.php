<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, string>  $detalles
     */
    public function __construct(
        public string $titulo,
        public string $mensaje,
        public array $detalles = [],
        public ?string $accionUrl = null,
        public ?string $accionTexto = null,
    ) {}

    public function build()
    {
        return $this->subject('[Admin] '.$this->titulo)
            ->view('emails.admin.alert');
    }
}
