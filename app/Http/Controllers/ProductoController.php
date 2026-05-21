<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\DetallePedido;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{

    public function details($idproducto, $slug_producto)
    {
        $producto = Producto::with(['imagenes', 'categoria', 'marca', 'comentarios.usuario'])
            ->where('Id_Producto', $idproducto)
            ->firstOrFail();

        // Redireccion canonica si el slug no coincide con el producto solicitado.
        if ($producto->Prod_Slug !== $slug_producto) {
            return redirect()->route('product.details', [
                'idproducto' => $producto->Id_Producto,
                'slug_producto' => $producto->Prod_Slug,
            ]);
        }

        $imagenPrincipal = $producto->imagenes->sortBy('orden')->first();
        $imagenUrl = $imagenPrincipal
            ? asset($imagenPrincipal->url)
            : asset('storage/products/default.png');

        $comentarios = $producto->comentarios->sortByDesc('Id_Comentario')->values();
        $totalReviews = $comentarios->count();
        $averageRating = round($comentarios->avg('Rating') ?? 0, 2);

        $ratingBreakdown = collect([5, 4, 3, 2, 1])->map(function ($stars) use ($comentarios, $totalReviews) {
            $count = $comentarios->where('Rating', $stars)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;

            return [
                'stars' => $stars,
                'count' => $count,
                'percentage' => $percentage,
            ];
        });

        $userCanReview = false;
        $userReview = null;

        if (Auth::check()) {
            // Cambio: en este proyecto Auth::id() puede devolver correo; usamos PK real del usuario.
            $userId = Auth::user()->Id_Usuario;

            $userCanReview = DetallePedido::query()
                ->where('Id_Producto', $producto->Id_Producto)
                ->whereHas('pedido', function ($query) use ($userId) {
                    $query->where('Id_Usuario', $userId);
                })
                ->exists();

            $userReview = Comentario::query()
                ->where('Id_Producto', $producto->Id_Producto)
                ->where('Id_Usuario', $userId)
                ->first();
        }

        // Cambio: productos relacionados por misma categoria, excluyendo el producto actual.
        $relatedProducts = Producto::with(['imagenes', 'comentarios', 'categoria'])
            ->where('Id_Categoria', $producto->Id_Categoria)
            ->where('Id_Producto', '!=', $producto->Id_Producto)
            ->where('Prod_Activo', 1)
            ->take(8)
            ->get();

        // Cambio: fallback con productos activos si la categoria tiene pocos o ninguno.
        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Producto::with(['imagenes', 'comentarios', 'categoria'])
                ->where('Id_Producto', '!=', $producto->Id_Producto)
                ->where('Prod_Activo', 1)
                ->take(8)
                ->get();
        }

        return view('Products.details', compact(
            'producto',
            'imagenUrl',
            'comentarios',
            'totalReviews',
            'averageRating',
            'ratingBreakdown',
            'userCanReview',
            'userReview',
            'relatedProducts'
        ));
    }

    public function saveReview(Request $request, $idproducto)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $producto = Producto::query()->where('Id_Producto', $idproducto)->firstOrFail();
        // Cambio: usar Id_Usuario real para validar compra y guardar reseña.
        $userId = Auth::user()->Id_Usuario;

        $userCanReview = DetallePedido::query()
            ->where('Id_Producto', $producto->Id_Producto)
            ->whereHas('pedido', function ($query) use ($userId) {
                $query->where('Id_Usuario', $userId);
            })
            ->exists();

        if (!$userCanReview) {
            return redirect()
                ->route('product.details', [
                    'idproducto' => $producto->Id_Producto,
                    'slug_producto' => $producto->Prod_Slug,
                ])
                ->withErrors(['review' => 'Solo puedes reseñar productos que ya compraste.']);
        }

        Comentario::updateOrCreate(
            [
                'Id_Usuario' => $userId,
                'Id_Producto' => $producto->Id_Producto,
            ],
            [
                'Rating' => (int) $request->rating,
                'Comentario' => $request->comentario,
            ]
        );

        return redirect()
            ->route('product.details', [
                'idproducto' => $producto->Id_Producto,
                'slug_producto' => $producto->Prod_Slug,
            ])
            ->with('review_status', 'Tu reseña se guardó correctamente.');
    }


    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show(Producto $producto)
    {
        //
    }

    public function edit(Producto $producto)
    {
        //
    }

    public function update(Request $request, Producto $producto)
    {
        //
    }

    public function destroy(Producto $producto)
    {
        //
    }

    public function shop(Request $request)
    {
        $query = Producto::with(['imagenes', 'categoria', 'marca', 'comentarios'])
            ->where('Prod_Activo', 1);

        // Búsqueda por nombre: el parámetro ?search= llega desde el formulario del header.
        if ($request->filled('search')) {
            $query->where('Prod_Nombre', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('Id_Categoria', $request->integer('category'));
        }

        if ($request->filled('brand')) {
            $query->where('Id_Marca', $request->integer('brand'));
        }

        if ($request->filled('min_price')) {
            $query->where('Prod_Precio', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('Prod_Precio', '<=', $request->max_price);
        }

        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            $comentarioTable = (new Comentario)->getTable();

            // Igual que la vista: solo productos cuyo round(promedio) coincide con las estrellas elegidas.
            $query->whereIn('Id_Producto', function ($sub) use ($rating, $comentarioTable) {
                $sub->select('Id_Producto')
                    ->from($comentarioTable)
                    ->groupBy('Id_Producto')
                    ->havingRaw('ROUND(AVG(Rating), 0) = ?', [$rating]);
            });
        }

        $products = $query->orderByDesc('Id_Producto')
            ->paginate(12)
            ->withQueryString();

        $categories = Categoria::orderBy('Cate_Nombre')->get();
        $brands = Marca::orderBy('Nom_Marca')->get();

        return view('shop.index', compact('products', 'categories', 'brands'));
    }

}
