<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoEnviadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pedido $pedido
    ) {}

    public function build()
    {
        return $this->subject('Pedido enviado - '.$this->pedido->Ped_Numero)
            ->view('emails.pedido.enviado', [
                'pedido' => $this->pedido,
            ]);
    }
}
