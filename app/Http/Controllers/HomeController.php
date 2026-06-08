<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Services\HomeBrandMarqueeService;
use App\Services\HomeRecommendationService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        protected HomeRecommendationService $homeRecommendationService,
        protected HomeBrandMarqueeService $homeBrandMarqueeService,
    ) {}

    public function index()
    {
        $topSellingProducts = Producto::with(['imagenes', 'comentarios', 'categoria'])
            ->withCount('carritodetalles')
            ->where('Prod_Activo', 1)
            ->orderByDesc('carritodetalles_count')
            ->take(12)
            ->get();

        $idUsuario = Auth::check()
            ? (int) Auth::user()->Id_Usuario
            : null;

        $recommendedProducts = $this->homeRecommendationService->recomendadosParaHome(
            $idUsuario,
            $topSellingProducts->pluck('Id_Producto')->all(),
            (int) config('catalog.home_recommended_limit', 5),
        );

        $securityCategory = Categoria::query()
            ->where('Cate_Slug', config('catalog.home_security_category_slug'))
            ->first();

        $securityProducts = collect();

        if ($securityCategory) {
            $securityProducts = Producto::with(['imagenes', 'comentarios', 'categoria'])
                ->where('Prod_Activo', 1)
                ->where('Id_Categoria', $securityCategory->Id_Categoria)
                ->orderByDesc('Id_Producto')
                ->take((int) config('catalog.home_security_products_limit', 8))
                ->get();
        }

        $brandMarqueeItems = $this->homeBrandMarqueeService->itemsParaHome();

        return view('welcome', compact(
            'topSellingProducts',
            'recommendedProducts',
            'brandMarqueeItems',
            'securityCategory',
            'securityProducts',
        ));
    }
}
