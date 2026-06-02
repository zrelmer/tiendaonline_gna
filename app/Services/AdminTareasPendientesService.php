<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\AdminInventarioService;
use App\Services\PedidoService;
use App\Support\EstatusCatalog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminTareasPendientesService
{
    public function __construct(
        protected AdminInventarioService $adminInventarioService
    ) {}

    /**
     * @return Collection<int, object{
     *     id: string,
     *     titulo: string,
     *     descripcion: string,
     *     fecha: Carbon,
     * }>
     */
    public function list(int $limit = 5): Collection
    {
        $tareas = collect();

        Pago::query()
            ->with(['pedido.boletaPago', 'estatus'])
            ->whereIn('Id_Estatus', [
                EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE,
                EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
                EstatusCatalog::PAGO_PENDIENTE_COBRO,
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (Pago $pago) use ($tareas) {
                $numero = $pago->pedido?->Ped_Numero ?? '—';
                $boleta = $pago->pedido?->boletaPago;
                $esVerificacionTransferencia = (int) $pago->Id_Estatus === EstatusCatalog::PAGO_PENDIENTE_VERIFICACION
                    && (int) $pago->Id_MetodoPago === PedidoService::METODO_TRANSFERENCIA
                    && $boleta !== null;

                $tareas->push((object) [
                    'id' => 'pago-'.$pago->Id_Pago,
                    'titulo' => $esVerificacionTransferencia
                        ? 'Aprobar boleta · '.$numero
                        : 'Verificar pago · '.$numero,
                    'descripcion' => ($pago->estatus?->Nom_Estatus ?? 'Pago pendiente')
                        .' · '.$this->tiempoRelativo($pago->created_at),
                    'fecha' => $pago->created_at ?? now(),
                    'url' => $esVerificacionTransferencia
                        ? route('admin.boletas.show', $boleta)
                        : null,
                ]);
            });

        Cotizacion::query()
            ->with('estatus')
            ->whereIn('Id_Estatus', [
                EstatusCatalog::COTIZACION_SOLICITUD_RECIBIDA,
                EstatusCatalog::COTIZACION_EN_REVISION,
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (Cotizacion $cotizacion) use ($tareas) {
                $tareas->push((object) [
                    'id' => 'cotizacion-'.$cotizacion->Id_Cotizacion,
                    'titulo' => 'Revisar cotización '.$cotizacion->Cot_Numero,
                    'descripcion' => ($cotizacion->estatus?->Nom_Estatus ?? 'Cotización pendiente')
                        .' · '.$this->tiempoRelativo($cotizacion->created_at),
                    'fecha' => $cotizacion->created_at ?? now(),
                    'url' => route('admin.cotizaciones.show', $cotizacion),
                ]);
            });

        $this->adminInventarioService
            ->productosBajoStockParaAlertas($limit)
            ->each(function (Producto $producto) use ($tareas) {
                $inventario = $producto->inventario;
                $stock = (int) ($inventario?->Stock ?? 0);
                $reservado = (int) ($inventario?->Stock_Reservado ?? 0);
                $disponible = max(0, $stock - $reservado);

                $tareas->push((object) [
                    'id' => 'inventario-'.$producto->Id_Producto,
                    'titulo' => 'Stock bajo · '.$producto->Prod_Nombre,
                    'descripcion' => 'Disponible: '.$disponible
                        .' (umbral ≤ '.$this->adminInventarioService->umbralBajoStock().')',
                    'fecha' => $inventario?->Ultima_Actualizacion
                        ? Carbon::parse($inventario->Ultima_Actualizacion)
                        : now(),
                    'url' => route('admin.inventario.ajustar', $producto),
                ]);
            });

        Pedido::query()
            ->visibleEnAdmin()
            ->with('estatus')
            ->whereIn('Id_Estatus', [
                EstatusCatalog::PEDIDO_PENDIENTE,
                EstatusCatalog::PEDIDO_CONFIRMADO,
                EstatusCatalog::PEDIDO_EN_PREPARACION,
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->each(function (Pedido $pedido) use ($tareas) {
                $tareas->push((object) [
                    'id' => 'pedido-'.$pedido->Id_Pedido,
                    'titulo' => 'Atender pedido '.$pedido->Ped_Numero,
                    'descripcion' => ($pedido->estatus?->Nom_Estatus ?? 'Pedido pendiente')
                        .' · '.$this->tiempoRelativo($pedido->created_at),
                    'fecha' => $pedido->created_at ?? now(),
                    'url' => route('admin.pedidos.seguimiento', $pedido),
                ]);
            });

        return $tareas
            ->sortByDesc(fn ($tarea) => $tarea->fecha)
            ->take($limit)
            ->values();
    }

    private function tiempoRelativo(?Carbon $fecha): string
    {
        if (! $fecha) {
            return 'reciente';
        }

        return $fecha->locale('es')->diffForHumans();
    }
}
