<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

class AdminMarcaService
{
    /**
     * @param  array<string, mixed>  $datos
     */
    public function crear(array $datos, ?UploadedFile $logo = null): Marca
    {
        return Marca::query()->create([
            'Nom_Marca' => $datos['Nom_Marca'],
            'slug_Marca' => $datos['slug_Marca'],
            'Descrip_Marca' => $datos['Descrip_Marca'],
            'Marc_Logo' => $this->guardarLogo($logo),
        ]);
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    public function actualizar(Marca $marca, array $datos, ?UploadedFile $logo = null): Marca
    {
        $logoRuta = $marca->Marc_Logo;

        if ($logo instanceof UploadedFile && $logo->isValid()) {
            $this->eliminarArchivoLogo($marca->Marc_Logo);
            $logoRuta = $this->guardarLogo($logo);
        }

        $marca->update([
            'Nom_Marca' => $datos['Nom_Marca'],
            'slug_Marca' => $datos['slug_Marca'],
            'Descrip_Marca' => $datos['Descrip_Marca'],
            'Marc_Logo' => $logoRuta,
        ]);

        return $marca->fresh();
    }

    public function eliminar(Marca $marca): void
    {
        if ($marca->productos()->exists()) {
            throw ValidationException::withMessages([
                'marca' => 'No se puede eliminar: la marca tiene uno o más productos asociados.',
            ]);
        }

        $this->eliminarArchivoLogo($marca->Marc_Logo);
        $marca->delete();
    }

    private function guardarLogo(?UploadedFile $logo): ?string
    {
        if (! $logo instanceof UploadedFile || ! $logo->isValid()) {
            return null;
        }

        $directorio = public_path('storage/brands');
        File::ensureDirectoryExists($directorio);

        $nombre = $logo->hashName();
        $logo->move($directorio, $nombre);

        return 'storage/brands/'.$nombre;
    }

    private function eliminarArchivoLogo(?string $url): void
    {
        if ($url === null || $url === '') {
            return;
        }

        if (! str_starts_with($url, 'storage/brands/')) {
            return;
        }

        $rutaPublica = public_path($url);
        if (File::isFile($rutaPublica)) {
            File::delete($rutaPublica);
        }
    }
}
