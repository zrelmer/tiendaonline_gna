<?php

namespace App\Services;

use App\Models\Producto;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProductoExportService
{
    public function descargarCsv(string $terminoBusqueda = ''): StreamedResponse
    {
        $productos = Producto::query()
            ->with(['categoria', 'marca', 'inventario', 'estatus', 'imagenes'])
            ->buscarAdmin($terminoBusqueda)
            ->orderByDesc('Id_Producto')
            ->get();

        $nombreArchivo = 'productos-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($productos) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID',
                'Nombre',
                'Slug',
                'Categoría',
                'Marca',
                'Precio',
                'Precio oferta',
                'Stock',
                'Activo',
                'Estatus',
                'Imágenes',
            ]);

            foreach ($productos as $producto) {
                fputcsv($handle, [
                    $producto->Id_Producto,
                    $producto->Prod_Nombre,
                    $producto->Prod_Slug,
                    $producto->categoria?->Cate_Nombre ?? '',
                    $producto->marca?->Nom_Marca ?? '',
                    $producto->Prod_Precio,
                    $producto->Prod_PrecioOferta ?? '',
                    $producto->inventario?->Stock ?? 0,
                    $producto->Prod_Activo ? 'Sí' : 'No',
                    $producto->estatus?->Nom_Estatus ?? '',
                    $producto->imagenes->sortBy('orden')->pluck('url')->implode(' | '),
                ]);
            }

            fclose($handle);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
