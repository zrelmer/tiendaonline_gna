<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\InventarioHistorial;
use App\Models\Movimiento;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminInventarioService
{
    public const FILTRO_TODOS = 'todos';

    public const FILTRO_BAJO_STOCK = 'bajo_stock';

    public const FILTRO_SIN_INVENTARIO = 'sin_inventario';

    public const FILTRO_SIN_STOCK = 'sin_stock';

    public const FILTRO_CON_STOCK = 'con_stock';

    public const TIPO_ENTRADA = 'entrada';

    public const TIPO_SALIDA = 'salida';

    public const TIPO_FIJAR = 'fijar';

    private const MOVIMIENTO_AJUSTE = 'Ajuste manual';

    public function umbralBajoStock(): int
    {
        return max(0, (int) config('inventario.umbral_bajo_stock', 5));
    }

    public function filtroDesdeRequest(?string $filtro): ?string
    {
        $filtro = trim((string) $filtro);

        if ($filtro === '' || $filtro === self::FILTRO_TODOS) {
            return null;
        }

        $permitidos = [
            self::FILTRO_BAJO_STOCK,
            self::FILTRO_SIN_INVENTARIO,
            self::FILTRO_SIN_STOCK,
            self::FILTRO_CON_STOCK,
        ];

        return in_array($filtro, $permitidos, true) ? $filtro : null;
    }

    public function etiquetaFiltro(?string $filtro): string
    {
        return match ($filtro) {
            self::FILTRO_BAJO_STOCK => 'Stock bajo',
            self::FILTRO_SIN_INVENTARIO => 'Sin registro',
            self::FILTRO_SIN_STOCK => 'Sin stock',
            self::FILTRO_CON_STOCK => 'Con stock',
            default => 'Todos',
        };
    }

    /**
     * @return array<string, int>
     */
    public function conteosFiltro(): array
    {
        $umbral = $this->umbralBajoStock();

        return [
            self::FILTRO_BAJO_STOCK => $this->queryBaseProductos()
                ->filtroInventarioAdmin(self::FILTRO_BAJO_STOCK, $umbral)
                ->count(),
            self::FILTRO_SIN_INVENTARIO => $this->queryBaseProductos()
                ->filtroInventarioAdmin(self::FILTRO_SIN_INVENTARIO, $umbral)
                ->count(),
            self::FILTRO_SIN_STOCK => $this->queryBaseProductos()
                ->filtroInventarioAdmin(self::FILTRO_SIN_STOCK, $umbral)
                ->count(),
            self::FILTRO_CON_STOCK => $this->queryBaseProductos()
                ->filtroInventarioAdmin(self::FILTRO_CON_STOCK, $umbral)
                ->count(),
        ];
    }

    public function contarBajoStock(): int
    {
        return $this->conteosFiltro()[self::FILTRO_BAJO_STOCK];
    }

    public function contarSinInventario(): int
    {
        return $this->conteosFiltro()[self::FILTRO_SIN_INVENTARIO];
    }

    /**
     * @return Collection<int, Producto>
     */
    public function productosBajoStockParaAlertas(int $limit = 5): Collection
    {
        $umbral = $this->umbralBajoStock();

        return $this->queryBaseProductos()
            ->with('inventario')
            ->filtroInventarioAdmin(self::FILTRO_BAJO_STOCK, $umbral)
            ->orderBy('Prod_Nombre')
            ->orderBy('Id_Producto')
            ->limit(max(1, $limit))
            ->get();
    }

    /**
     * @return array{
     *     total_productos: int,
     *     bajo_stock: int,
     *     sin_inventario: int,
     *     unidades_disponibles: int,
     *     umbral: int,
     * }
     */
    public function kpis(): array
    {
        $umbral = $this->umbralBajoStock();
        $conteos = $this->conteosFiltro();

        $unidadesDisponibles = (int) Inventario::query()
            ->selectRaw('COALESCE(SUM(Stock - Stock_Reservado), 0) as total')
            ->value('total');

        return [
            'total_productos' => Producto::query()->count(),
            'bajo_stock' => $conteos[self::FILTRO_BAJO_STOCK],
            'sin_inventario' => $conteos[self::FILTRO_SIN_INVENTARIO],
            'unidades_disponibles' => max(0, $unidadesDisponibles),
            'umbral' => $umbral,
        ];
    }

    /**
     * @return Builder<Producto>
     */
    public function queryBaseProductos(): Builder
    {
        return Producto::query();
    }

    /**
     * @return self::TIPO_*
     */
    public function tiposAjuste(): array
    {
        return [
            self::TIPO_ENTRADA,
            self::TIPO_SALIDA,
            self::TIPO_FIJAR,
        ];
    }

    public function etiquetaTipoAjuste(string $tipo): string
    {
        return match ($tipo) {
            self::TIPO_ENTRADA => 'Entrada',
            self::TIPO_SALIDA => 'Salida',
            self::TIPO_FIJAR => 'Fijar stock',
            default => '—',
        };
    }

    public function ajustar(Producto $producto, string $tipo, int $cantidad, ?string $comentario = null): Inventario
    {
        if (! in_array($tipo, $this->tiposAjuste(), true)) {
            throw ValidationException::withMessages([
                'tipo' => 'El tipo de ajuste no es válido.',
            ]);
        }

        return DB::transaction(function () use ($producto, $tipo, $cantidad, $comentario) {
            $inventario = Inventario::query()
                ->where('Id_Producto', $producto->Id_Producto)
                ->lockForUpdate()
                ->first();

            if (! $inventario) {
                $inventario = Inventario::query()->create([
                    'Id_Producto' => $producto->Id_Producto,
                    'Stock' => 0,
                    'Stock_Reservado' => 0,
                ]);
            }

            $stockAntes = (int) $inventario->Stock;
            $reservado = (int) $inventario->Stock_Reservado;
            $stockDespues = match ($tipo) {
                self::TIPO_ENTRADA => $stockAntes + $cantidad,
                self::TIPO_SALIDA => $stockAntes - $cantidad,
                self::TIPO_FIJAR => $cantidad,
            };

            $this->validarStockResultante($producto, $tipo, $cantidad, $stockAntes, $reservado, $stockDespues);

            $delta = abs($stockDespues - $stockAntes);

            if ($delta === 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'El ajuste no modifica el stock actual.',
                ]);
            }

            $inventario->update([
                'Stock' => $stockDespues,
            ]);

            InventarioHistorial::query()->create([
                'Id_Inventario' => $inventario->Id_Inventario,
                'Id_Movimiento' => $this->idMovimientoAjuste(),
                'Cantidad' => $delta,
                'Stock_Antes' => $stockAntes,
                'Stock_Despues' => $stockDespues,
                'Referencia' => $this->referenciaAjuste($producto, $tipo, $comentario),
                'Fecha_Movimiento' => now(),
            ]);

            return $inventario->fresh(['producto']);
        });
    }

    private function validarStockResultante(
        Producto $producto,
        string $tipo,
        int $cantidad,
        int $stockAntes,
        int $reservado,
        int $stockDespues
    ): void {
        $nombreProducto = $producto->Prod_Nombre;
        $disponible = max(0, $stockAntes - $reservado);

        if ($stockDespues < 0) {
            throw ValidationException::withMessages([
                'cantidad' => "El stock de «{$nombreProducto}» no puede quedar negativo.",
            ]);
        }

        if ($stockDespues < $reservado) {
            throw ValidationException::withMessages([
                'cantidad' => "El stock final no puede ser menor al reservado ({$reservado} unidades).",
            ]);
        }

        if ($tipo === self::TIPO_SALIDA && $cantidad > $disponible) {
            throw ValidationException::withMessages([
                'cantidad' => "Stock disponible insuficiente para «{$nombreProducto}». Disponible: {$disponible}.",
            ]);
        }
    }

    private function idMovimientoAjuste(): int
    {
        return (int) Movimiento::query()->firstOrCreate([
            'Nom_Movimiento' => self::MOVIMIENTO_AJUSTE,
        ])->Id_Movimiento;
    }

    private function referenciaAjuste(Producto $producto, string $tipo, ?string $comentario): string
    {
        $base = sprintf(
            'AJUSTE:%s:P%d:%s',
            strtoupper($tipo),
            $producto->Id_Producto,
            now()->format('YmdHis')
        );

        $comentario = trim((string) $comentario);

        if ($comentario === '') {
            return Str::limit($base, 200, '');
        }

        return Str::limit($base.'|'.preg_replace('/\s+/', ' ', $comentario), 200, '');
    }

    /**
     * @return Builder<InventarioHistorial>
     */
    public function queryHistorialBase(): Builder
    {
        return InventarioHistorial::query();
    }

    /**
     * @return Collection<int, Movimiento>
     */
    public function movimientosParaFiltro(): Collection
    {
        return Movimiento::query()
            ->orderBy('Nom_Movimiento')
            ->get();
    }

    /**
     * @return Collection<int, Producto>
     */
    public function productosConHistorialParaFiltro(): Collection
    {
        return Producto::query()
            ->whereHas('inventario.historial')
            ->orderBy('Prod_Nombre')
            ->get(['Id_Producto', 'Prod_Nombre']);
    }

    public function idPedidoDesdeReferencia(?string $referencia): ?int
    {
        if ($referencia === null || $referencia === '') {
            return null;
        }

        if (preg_match('/^PEDIDO:(\d+):/', $referencia, $coincidencias) !== 1) {
            return null;
        }

        return (int) $coincidencias[1];
    }

    public function esIncrementoStock(int $stockAntes, int $stockDespues): bool
    {
        return $stockDespues > $stockAntes;
    }

    public function signoMovimiento(int $stockAntes, int $stockDespues): string
    {
        if ($stockDespues > $stockAntes) {
            return '+';
        }

        if ($stockDespues < $stockAntes) {
            return '−';
        }

        return '';
    }

    public function idProductoDesdeRequest(mixed $valor): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        $id = (int) $valor;

        return $id > 0 ? $id : null;
    }

    public function idMovimientoDesdeRequest(mixed $valor): ?int
    {
        return $this->idProductoDesdeRequest($valor);
    }

    /**
     * @return array{desde: ?string, hasta: ?string}
     */
    public function fechasDesdeRequest(?string $desde, ?string $hasta): array
    {
        $desde = trim((string) $desde);
        $hasta = trim((string) $hasta);

        return [
            'desde' => $desde !== '' ? $desde : null,
            'hasta' => $hasta !== '' ? $hasta : null,
        ];
    }

    public function registrarStockInicialAlta(Producto $producto, Inventario $inventario, int $stock): void
    {
        if ($stock <= 0) {
            return;
        }

        InventarioHistorial::query()->create([
            'Id_Inventario' => $inventario->Id_Inventario,
            'Id_Movimiento' => $this->idMovimientoAjuste(),
            'Cantidad' => $stock,
            'Stock_Antes' => 0,
            'Stock_Despues' => $stock,
            'Referencia' => sprintf(
                'ALTA:PRODUCTO:P%d:%s',
                $producto->Id_Producto,
                now()->format('YmdHis')
            ),
            'Fecha_Movimiento' => now(),
        ]);
    }
}
