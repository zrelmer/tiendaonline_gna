<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoGeneradoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pedido $pedido,
        public bool $pagoConfirmado = false
    ) {}

    public function build()
    {
        $asunto = $this->pagoConfirmado
            ? 'Pago confirmado - Pedido '.$this->pedido->Ped_Numero
            : 'Pedido generado - '.$this->pedido->Ped_Numero;

        return $this->subject($asunto)
            ->view('emails.pedido.generado', [
                'pedido' => $this->pedido,
                'pagoConfirmado' => $this->pagoConfirmado,
            ]);
    }
}

