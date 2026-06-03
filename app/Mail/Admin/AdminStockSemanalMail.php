<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AdminStockSemanalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\Producto>  $productosBajoStock
     * @param  Collection<int, \App\Models\Producto>  $productosSinStock
     */
    public function __construct(
        public Collection $productosBajoStock,
        public Collection $productosSinStock,
        public int $umbral,
        public string $fecha,
        public string $accionUrl,
    ) {}

    public function build()
    {
        $total = $this->productosBajoStock->count() + $this->productosSinStock->count();

        return $this->subject('[Admin] Resumen semanal de inventario ('.$total.' alertas)')
            ->view('emails.admin.stock-semanal');
    }
}
