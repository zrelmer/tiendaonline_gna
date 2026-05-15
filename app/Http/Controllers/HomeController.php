<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        $topSellingProducts = Producto::withCount('carritodetalles') // Contar la cantidad de veces
            ->orderByDesc('carritodetalles_count') // Ordenar por los más vendidos
            ->take(12) // Tomar solo 24 productos
            ->get();

        return view('welcome', compact('topSellingProducts'));
    }
}
