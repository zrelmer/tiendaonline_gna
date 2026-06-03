<?php

namespace App\View\Composers;

use App\Models\Categoria;
use Illuminate\View\View;

class ShopNavComposer
{
    public function compose(View $view): void
    {
        $view->with(
            'navCategories',
            Categoria::query()
                ->orderBy('Cate_Nombre')
                ->get()
        );
    }
}
