<?php

namespace Tests\Feature;

use App\Models\Cotizacion;
use App\Models\Usuario;
use App\Services\AdminCotizacionService;
use App\Services\CotizacionService;
use App\Support\EstatusCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\SeedsCatalog;
use Tests\TestCase;

class CotizacionFlowTest extends TestCase
{
    use RefreshDatabase;
    use SeedsCatalog;

    public function test_user_can_request_admin_emit_and_client_accepts_cotizacion(): void
    {
        Storage::fake('public');
        $this->seedRolesAndStatuses();

        $cliente = Usuario::factory()->create();
        $admin = Usuario::factory()->admin()->create();
        $producto = $this->createProducto();

        $this->actingAs($cliente)
            ->post(route('dashboard.cotizaciones.store'), [
                'nombre_cliente' => 'Cliente prueba',
                'nit' => '1234567-8',
                'direccion' => 'Zona 1',
                'email' => $cliente->Usu_Correo,
                'notas' => 'Urgente',
                'items' => [
                    [
                        'id_producto' => $producto->Id_Producto,
                        'descripcion' => '',
                        'cantidad' => 2,
                    ],
                ],
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $cotizacion = Cotizacion::query()->first();
        $this->assertNotNull($cotizacion);
        $this->assertSame(EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA, (int) $cotizacion->Id_Estatus);

        $this->actingAs($admin)
            ->post(route('admin.cotizaciones.revision', $cotizacion), [
                'comentario' => 'Revisando stock',
            ])
            ->assertRedirect(route('admin.cotizaciones.show', $cotizacion));

        $cotizacion->refresh();
        $this->assertSame(EstatusCatalog::COTIZACION_EN_REVISION, (int) $cotizacion->Id_Estatus);

        $cotizacion->load('detalle');
        $lineas = [];
        foreach ($cotizacion->detalle as $detalle) {
            $lineas[(string) $detalle->Id_CotizacionDetalle] = [
                'costo_unit' => 150.50,
            ];
        }

        $this->actingAs($admin)
            ->post(route('admin.cotizaciones.emitir.store', $cotizacion), [
                'lineas' => $lineas,
                'terminos' => "Vigencia 10 días.\nPago contra entrega.",
                'vigencia_dias' => 10,
                'archivo' => UploadedFile::fake()->create('cotizacion.pdf', 100, 'application/pdf'),
                'comentario' => 'Cotización lista',
            ])
            ->assertRedirect(route('admin.cotizaciones.show', $cotizacion))
            ->assertSessionHas('success');

        $cotizacion->refresh();
        $this->assertSame(EstatusCatalog::COTIZACION_EMITIDA, (int) $cotizacion->Id_Estatus);
        $this->assertTrue($cotizacion->archivoDisponible());

        $this->actingAs($cliente)
            ->post(route('dashboard.cotizaciones.aceptar', $cotizacion), [
                'comentario' => 'Acepto los términos',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $cotizacion->refresh();
        $this->assertSame(EstatusCatalog::COTIZACION_ACEPTADA, (int) $cotizacion->Id_Estatus);
    }

    public function test_client_can_reject_emitted_cotizacion(): void
    {
        $this->seedRolesAndStatuses();

        $cliente = Usuario::factory()->create();
        $cotizacion = app(CotizacionService::class)->crearSolicitud([
            'nombre_cliente' => 'Cliente',
            'items' => [
                ['descripcion' => 'Servicio X', 'cantidad' => 1],
            ],
        ], (int) $cliente->Id_Usuario);

        app(AdminCotizacionService::class)->marcarEnRevision($cotizacion);

        Storage::fake('public');
        $cotizacion->load('detalle');
        $lineas = [];
        foreach ($cotizacion->detalle as $detalle) {
            $lineas[(string) $detalle->Id_CotizacionDetalle] = ['costo_unit' => 50];
        }

        app(AdminCotizacionService::class)->emitir(
            $cotizacion,
            $lineas,
            'Términos de prueba',
            5,
            UploadedFile::fake()->create('cot.pdf', 50, 'application/pdf')
        );

        $this->actingAs($cliente)
            ->post(route('dashboard.cotizaciones.rechazar', $cotizacion), [
                'comentario' => 'Precios fuera de presupuesto',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertSame(
            EstatusCatalog::COTIZACION_RECHAZADA,
            (int) $cotizacion->fresh()->Id_Estatus
        );
    }

    public function test_emitted_cotizacion_becomes_vencida_after_vigencia(): void
    {
        Carbon::setTestNow('2026-06-01 10:00:00');

        try {
            $this->seedRolesAndStatuses();
            $cliente = Usuario::factory()->create();

            $cotizacion = app(CotizacionService::class)->crearSolicitud([
                'nombre_cliente' => 'Cliente',
                'items' => [
                    ['descripcion' => 'Item', 'cantidad' => 1],
                ],
            ], (int) $cliente->Id_Usuario);

            app(AdminCotizacionService::class)->marcarEnRevision($cotizacion);

            Storage::fake('public');
            $cotizacion->load('detalle');
            $detalleId = (string) $cotizacion->detalle->first()->Id_CotizacionDetalle;

            app(AdminCotizacionService::class)->emitir(
                $cotizacion,
                [$detalleId => ['costo_unit' => 10]],
                'Términos',
                3,
                UploadedFile::fake()->create('cot.pdf', 50, 'application/pdf')
            );

            Carbon::setTestNow('2026-06-05 10:00:00');

            $actualizadas = app(CotizacionService::class)->sincronizarVencidas((int) $cliente->Id_Usuario);

            $this->assertSame(1, $actualizadas);
            $this->assertSame(
                EstatusCatalog::COTIZACION_VENCIDA,
                (int) $cotizacion->fresh()->Id_Estatus
            );
        } finally {
            Carbon::setTestNow();
        }
    }
}
