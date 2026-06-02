<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Producto;
use App\Models\Usuario;
use App\Services\CotizacionService;
use App\Services\PedidoService;
use App\Support\EstatusCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected PedidoService $pedidoService,
        protected CotizacionService $cotizacionService
    ) {}

    public function index(Request $request): View
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $totalPedidos = $usuario->pedidos()->count();

        $pedidosPendientes = $usuario->pedidos()
            ->whereIn('Id_Estatus', [
                EstatusCatalog::PEDIDO_PENDIENTE,
                EstatusCatalog::PEDIDO_CONFIRMADO,
                EstatusCatalog::PEDIDO_EN_PREPARACION,
                EstatusCatalog::PEDIDO_ENVIADO,
            ])
            ->count();

        $totalListaDeseos = $usuario->listadeseos()->count();

        $pedidos = $usuario->pedidos()
            ->with([
                'estatus',
                'pago.metodoPago',
                'pago.estatus',
                'detalle.producto.imagenes',
                'direccion.municipio.departamento',
                'envio.estatus',
                'historial.estatus',
            ])
            ->latest('Id_Pedido')
            ->get();

        $direcciones = $usuario->direcciones()
            ->with(['municipio.departamento'])
            ->withCount('pedido')
            ->get();

        $departamentos = Departamento::query()
            ->with(['municipios' => fn ($query) => $query->orderBy('Nom_Municipio')])
            ->orderBy('Nom_Departamento')
            ->get();

        $municipiosPorDepartamento = $departamentos->mapWithKeys(fn ($departamento) => [
            $departamento->Id_Departamento => $departamento->municipios->map(fn ($municipio) => [
                'id' => $municipio->Id_Municipio,
                'nombre' => $municipio->Nom_Municipio,
            ])->values(),
        ]);

        $this->cotizacionService->sincronizarVencidas((int) $usuario->Id_Usuario);

        $cotizaciones = $usuario->cotizaciones()
            ->with(['estatus', 'detalle.producto', 'historial.estatus'])
            ->latest('Id_Cotizacion')
            ->get();

        $productosCotizacion = Producto::query()
            ->where('Prod_Activo', 1)
            ->orderBy('Prod_Nombre')
            ->get(['Id_Producto', 'Prod_Nombre', 'Prod_Precio']);

        $direccionPrincipal = $direcciones->first();

        return view('dashboard', compact(
            'usuario',
            'totalPedidos',
            'pedidosPendientes',
            'totalListaDeseos',
            'pedidos',
            'direcciones',
            'departamentos',
            'municipiosPorDepartamento',
            'cotizaciones',
            'productosCotizacion',
            'direccionPrincipal',
        ));
    }
}
