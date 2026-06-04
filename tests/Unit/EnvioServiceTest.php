<?php

namespace Tests\Unit;

use App\Models\CarritoDetalle;
use App\Services\EnvioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsCatalog;
use Tests\TestCase;

class EnvioServiceTest extends TestCase
{
    use RefreshDatabase;
    use SeedsCatalog;

    protected EnvioService $envioService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRolesAndStatuses();
        $this->envioService = app(EnvioService::class);
    }

    public function test_categoria_slug_licencia_no_cobra_envio(): void
    {
        $categoria = $this->createCategoria([
            'Cate_Nombre' => 'Antivirus y seguridad',
            'Cate_Slug' => 'licencia',
        ]);

        $producto = $this->createProducto($categoria);

        $this->assertTrue($this->envioService->esProductoDigital($producto));

        $linea = new CarritoDetalle([
            'Id_Producto' => $producto->Id_Producto,
            'Cantidad' => 1,
            'Precio' => 50,
        ]);
        $linea->setRelation('producto', $producto);

        $costo = $this->envioService->calcularCosto(50, collect([$linea]));

        $this->assertSame(0.0, $costo);
    }

    public function test_categoria_fisica_cobra_envio_bajo_umbral(): void
    {
        $categoria = $this->createCategoria([
            'Cate_Nombre' => 'Laptops',
            'Cate_Slug' => 'laptops',
        ]);

        $producto = $this->createProducto($categoria);

        $this->assertFalse($this->envioService->esProductoDigital($producto));

        $linea = new CarritoDetalle([
            'Id_Producto' => $producto->Id_Producto,
            'Cantidad' => 1,
            'Precio' => 100,
        ]);
        $linea->setRelation('producto', $producto);

        $costo = $this->envioService->calcularCosto(100, collect([$linea]));

        $this->assertSame(35.0, $costo);
    }
}
