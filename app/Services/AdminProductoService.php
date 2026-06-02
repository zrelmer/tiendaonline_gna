<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\InventarioHistorial;
use App\Models\ProdImagen;
use App\Models\Producto;
use App\Support\RichText;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class AdminProductoService
{
    public function __construct(
        protected AdminInventarioService $adminInventarioService
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     * @param  array<int, UploadedFile>  $imagenes
     */
    public function crear(array $datos, array $imagenes = []): Producto
    {
        return DB::transaction(function () use ($datos, $imagenes) {
            $producto = Producto::query()->create([
                'Id_Categoria' => (int) $datos['Id_Categoria'],
                'Id_Marca' => (int) $datos['Id_Marca'],
                'Prod_Nombre' => $datos['Prod_Nombre'],
                'Prod_Slug' => $datos['Prod_Slug'],
                'Prod_Descripcion' => RichText::sanitize($datos['Prod_Descripcion'] ?? ''),
                'Prod_Precio' => $datos['Prod_Precio'],
                'Prod_PrecioOferta' => $datos['Prod_PrecioOferta'] ?? null,
                'Id_Estatus' => (int) $datos['Id_Estatus'],
                'Prod_Activo' => (bool) $datos['Prod_Activo'],
            ]);

            $inventario = Inventario::query()->create([
                'Id_Producto' => $producto->Id_Producto,
                'Stock' => (int) $datos['Stock'],
                'Stock_Reservado' => (int) ($datos['Stock_Reservado'] ?? 0),
            ]);

            $this->adminInventarioService->registrarStockInicialAlta(
                $producto,
                $inventario,
                (int) $datos['Stock']
            );

            foreach ($imagenes as $orden => $archivo) {
                $this->guardarImagenProducto($producto, $archivo, $orden);
            }

            return $producto;
        });
    }

    /**
     * @param  array<string, mixed>  $datos
     * @param  array<int, UploadedFile>  $imagenesNuevas
     */
    public function actualizar(Producto $producto, array $datos, array $imagenesNuevas = []): Producto
    {
        return DB::transaction(function () use ($producto, $datos, $imagenesNuevas) {
            $producto->update([
                'Id_Categoria' => (int) $datos['Id_Categoria'],
                'Id_Marca' => (int) $datos['Id_Marca'],
                'Prod_Nombre' => $datos['Prod_Nombre'],
                'Prod_Slug' => $datos['Prod_Slug'],
                'Prod_Descripcion' => RichText::sanitize($datos['Prod_Descripcion'] ?? ''),
                'Prod_Precio' => $datos['Prod_Precio'],
                'Prod_PrecioOferta' => $datos['Prod_PrecioOferta'] ?? null,
                'Id_Estatus' => (int) $datos['Id_Estatus'],
                'Prod_Activo' => (bool) $datos['Prod_Activo'],
            ]);

            if (! $producto->inventario) {
                Inventario::query()->create([
                    'Id_Producto' => $producto->Id_Producto,
                    'Stock' => 0,
                    'Stock_Reservado' => 0,
                ]);
            }

            $ordenBase = (int) $producto->imagenes()->max('orden');

            foreach ($imagenesNuevas as $indice => $archivo) {
                $this->guardarImagenProducto($producto, $archivo, $ordenBase + 1 + $indice);
            }

            return $producto->fresh(['inventario', 'imagenes']);
        });
    }

    public function eliminar(Producto $producto): void
    {
        if ($producto->detallepedidos()->exists()) {
            throw ValidationException::withMessages([
                'producto' => 'No se puede eliminar: el producto aparece en uno o más pedidos.',
            ]);
        }

        DB::transaction(function () use ($producto) {
            $producto->load(['imagenes', 'inventario']);

            if ($producto->inventario) {
                InventarioHistorial::query()
                    ->where('Id_Inventario', $producto->inventario->Id_Inventario)
                    ->delete();

                $producto->inventario->delete();
            }

            $producto->carritodetalles()->delete();
            $producto->listadeseos()->delete();
            $producto->comentarios()->delete();

            foreach ($producto->imagenes as $imagen) {
                $this->eliminarRegistroImagen($imagen);
            }

            $producto->delete();
        });
    }

    private function guardarImagenProducto(Producto $producto, UploadedFile $archivo, int $orden): void
    {
        if (! $archivo->isValid()) {
            return;
        }

        $directorio = public_path('storage/products');
        File::ensureDirectoryExists($directorio);

        $nombre = $archivo->hashName();
        $archivo->move($directorio, $nombre);

        ProdImagen::query()->create([
            'Id_Producto' => $producto->Id_Producto,
            'url' => 'storage/products/'.$nombre,
            'orden' => $orden,
        ]);
    }

    private function eliminarRegistroImagen(ProdImagen $imagen): void
    {
        $url = $imagen->url;
        $imagen->delete();

        if (ProdImagen::query()->where('url', $url)->exists()) {
            return;
        }

        $rutaPublica = public_path($url);
        if (File::isFile($rutaPublica)) {
            File::delete($rutaPublica);
        }

        $rutaAlterna = storage_path('app/public/'.ltrim(str_replace('storage/', '', $url), '/'));
        if (File::isFile($rutaAlterna)) {
            File::delete($rutaAlterna);
        }
    }
}
