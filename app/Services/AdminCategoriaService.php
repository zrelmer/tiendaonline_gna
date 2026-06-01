<?php

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class AdminCategoriaService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos, ?UploadedFile $imagen = null): Categoria
    {
        return Categoria::query()->create([
            'Cate_Nombre' => $datos['Cate_Nombre'],
            'Cate_Slug' => $datos['Cate_Slug'],
            'Cate_Descripcion' => $datos['Cate_Descripcion'],
            'Cate_Imagen' => $this->guardarImagen($imagen),
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Categoria $categoria, array $datos, ?UploadedFile $imagen = null): Categoria
    {
        $imagenRuta = $categoria->Cate_Imagen;

        if ($imagen instanceof UploadedFile && $imagen->isValid()) {
            $this->eliminarArchivoImagen($categoria->Cate_Imagen);
            $imagenRuta = $this->guardarImagen($imagen);
        }

        $categoria->update([
            'Cate_Nombre' => $datos['Cate_Nombre'],
            'Cate_Slug' => $datos['Cate_Slug'],
            'Cate_Descripcion' => $datos['Cate_Descripcion'],
            'Cate_Imagen' => $imagenRuta,
        ]);

        return $categoria->fresh();
    }

    public function eliminar(Categoria $categoria): void
    {
        if ($categoria->productos()->exists()) {
            throw ValidationException::withMessages([
                'categoria' => 'No se puede eliminar: la categoría tiene uno o más productos asociados.',
            ]);
        }

        $this->eliminarArchivoImagen($categoria->Cate_Imagen);
        $categoria->delete();
    }

    private function guardarImagen(?UploadedFile $imagen): ?string
    {
        if (! $imagen instanceof UploadedFile || ! $imagen->isValid()) {
            return null;
        }

        $directorio = public_path('storage/categories');
        File::ensureDirectoryExists($directorio);

        $nombre = $imagen->hashName();
        $imagen->move($directorio, $nombre);

        return 'storage/categories/'.$nombre;
    }

    private function eliminarArchivoImagen(?string $url): void
    {
        if ($url === null || $url === '') {
            return;
        }

        if (! str_starts_with($url, 'storage/categories/')) {
            return;
        }

        $rutaPublica = public_path($url);
        if (File::isFile($rutaPublica)) {
            File::delete($rutaPublica);
        }
    }
}
