<?php

namespace App\Services;

use App\Models\Pago;
use Illuminate\Support\Collection;

class AdminRecentPagosService
{
    /**
     * @return Collection<int, Pago>
     */
    public function latest(int $limit = 5): Collection
    {
        return Pago::query()
            ->with([
                'pedido',
                'metodoPago',
                'estatus',
            ])
            ->whereHas('pedido')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
