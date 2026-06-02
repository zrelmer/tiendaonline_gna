<?php

namespace App\Services;

use App\Models\Pedido;
use Illuminate\Support\Collection;

class AdminRecentOrdersService
{
    /**
     * @return Collection<int, Pedido>
     */
    public function latest(int $limit = 5): Collection
    {
        return Pedido::query()
            ->visibleEnAdmin()
            ->with([
                'detalle.producto',
                'estatus',
                'pago.estatus',
                'pago.metodoPago',
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
