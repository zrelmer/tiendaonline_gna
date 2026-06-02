<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminInventarioAjusteRequest;
use App\Models\Producto;
use App\Services\AdminInventarioService;
use App\Services\AdminInventarioVentasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminInventarioController extends Controller
{
    public function __construct(
        protected AdminInventarioService $adminInventarioService,
        protected AdminInventarioVentasService $adminInventarioVentasService
    ) {}

    public function index(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $filtro = $this->adminInventarioService->filtroDesdeRequest(
            (string) $request->input('filtro', '')
        );
        $umbral = $this->adminInventarioService->umbralBajoStock();

        $productos = $this->adminInventarioService->queryBaseProductos()
            ->with(['inventario', 'categoria', 'imagenes'])
            ->buscarAdmin($terminoBusqueda)
            ->filtroInventarioAdmin($filtro, $umbral)
            ->orderBy('Prod_Nombre')
            ->orderBy('Id_Producto')
            ->paginate(15)
            ->withQueryString();

        $kpis = $this->adminInventarioService->kpis();
        $conteosFiltro = $this->adminInventarioService->conteosFiltro();

        return view('admin.inventario.index', compact(
            'productos',
            'terminoBusqueda',
            'filtro',
            'umbral',
            'kpis',
            'conteosFiltro',
        ));
    }

    public function ajustar(Producto $producto): View
    {
        $producto->load(['inventario', 'categoria']);

        $inventario = $producto->inventario;
        $stock = (int) ($inventario?->Stock ?? 0);
        $reservado = (int) ($inventario?->Stock_Reservado ?? 0);
        $disponible = max(0, $stock - $reservado);
        $sinRegistro = ! $inventario;

        return view('admin.inventario.ajustar', compact(
            'producto',
            'inventario',
            'stock',
            'reservado',
            'disponible',
            'sinRegistro',
        ));
    }

    public function ajustarStore(AdminInventarioAjusteRequest $request, Producto $producto): RedirectResponse
    {
        try {
            $this->adminInventarioService->ajustar(
                $producto,
                (string) $request->input('tipo'),
                (int) $request->input('cantidad'),
                $request->input('comentario')
            );
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.inventario.ajustar', $producto)
                ->withErrors($e->errors(), 'inventario')
                ->withInput();
        }

        return redirect()
            ->route('admin.inventario.index')
            ->with('success', 'Stock actualizado para «'.$producto->Prod_Nombre.'».');
    }

    public function historialIndex(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $idMovimiento = $this->adminInventarioService->idMovimientoDesdeRequest($request->input('movimiento'));
        $idProducto = $this->adminInventarioService->idProductoDesdeRequest($request->input('producto'));
        $fechas = $this->adminInventarioService->fechasDesdeRequest(
            (string) $request->input('fecha_desde', ''),
            (string) $request->input('fecha_hasta', '')
        );

        $movimientos = $this->adminInventarioService->queryHistorialBase()
            ->with(['inventario.producto', 'movimiento'])
            ->buscarAdmin($terminoBusqueda)
            ->filtroMovimientoAdmin($idMovimiento)
            ->filtroProductoAdmin($idProducto)
            ->filtroFechasAdmin($fechas['desde'], $fechas['hasta'])
            ->orderByDesc('Fecha_Movimiento')
            ->orderByDesc('Id_InventarioHistorial')
            ->paginate(20)
            ->withQueryString();

        return view('admin.inventario.historial-index', [
            'movimientos' => $movimientos,
            'terminoBusqueda' => $terminoBusqueda,
            'idMovimiento' => $idMovimiento,
            'idProducto' => $idProducto,
            'fechaDesde' => $fechas['desde'],
            'fechaHasta' => $fechas['hasta'],
            'tiposMovimiento' => $this->adminInventarioService->movimientosParaFiltro(),
            'productosFiltro' => $this->adminInventarioService->productosConHistorialParaFiltro(),
        ]);
    }

    public function ventasIndex(Request $request): View
    {
        $terminoBusqueda = trim((string) $request->input('q', ''));
        $fechaDesde = (string) $request->input('fecha_desde', '');
        $fechaHasta = (string) $request->input('fecha_hasta', '');

        $reporte = $this->adminInventarioVentasService->reportePorProducto(
            $fechaDesde,
            $fechaHasta,
            $terminoBusqueda
        );

        return view('admin.inventario.ventas-index', [
            'ventas' => $reporte['filas'],
            'totales' => $reporte['totales'],
            'terminoBusqueda' => $terminoBusqueda,
            'fechaDesde' => $fechaDesde !== '' ? $fechaDesde : null,
            'fechaHasta' => $fechaHasta !== '' ? $fechaHasta : null,
        ]);
    }
}
