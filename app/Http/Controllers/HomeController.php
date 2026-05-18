<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        $topSellingProducts = Producto::with(['imagenes', 'comentarios'])
            ->withCount('carritodetalles')
            ->where('Prod_Activo', 1)
            ->orderByDesc('carritodetalles_count')
            ->take(12)
            ->get();

        return view('welcome', compact('topSellingProducts'));
    }
}
