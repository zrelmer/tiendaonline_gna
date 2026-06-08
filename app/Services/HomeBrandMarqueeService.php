<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Support\Collection;

class HomeBrandMarqueeService
{
    /**
     * @return Collection<int, array{nombre: string, logo_url: string, shop_url: string}>
     */
    public function itemsParaHome(): Collection
    {
        return Marca::query()
            ->whereNotNull('Marc_Logo')
            ->where('Marc_Logo', '!=', '')
            ->orderBy('Nom_Marca')
            ->get(['Id_Marca', 'Nom_Marca', 'Marc_Logo'])
            ->map(fn (Marca $marca) => [
                'nombre' => $marca->Nom_Marca,
                'logo_url' => asset($marca->Marc_Logo),
                'shop_url' => route('shop.index', ['brand' => $marca->Id_Marca]),
            ])
            ->values();
    }
}
