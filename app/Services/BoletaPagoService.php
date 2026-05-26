<?php

namespace App\Services;

use App\Models\BoletaPago;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\PedidoHistorial;
use App\Support\EstatusCatalog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BoletaPagoService
{
    public function pedidoDelUsuario(int $idPedido, ?int $idUsuario = null): Pedido
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;

        return Pedido::query()
            ->where('Id_Pedido', $idPedido)
            ->where('Id_Usuario', $idUsuario)
            ->with('pago')
            ->firstOrFail();
    }

    /**
     * Pedidos con transferencia pendientes de subir comprobante.
     */
    public function pedidosPendientesDeBoleta(?int $idUsuario = null)
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;

        return Pedido::query()
            ->where('Id_Usuario', $idUsuario)
            ->whereDoesntHave('boletaPago')
            ->whereHas('pago', function ($query) {
                $query
                    ->where('Id_MetodoPago', PedidoService::METODO_TRANSFERENCIA)
                    ->where('Id_Estatus', EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE);
            })
            ->orderByDesc('Id_Pedido')
            ->get();
    }

    public function guardar(UploadedFile $archivo, int $idPedido, ?int $idUsuario = null): BoletaPago
    {
        $idUsuario ??= (int) Auth::user()->Id_Usuario;

        return DB::transaction(function () use ($archivo, $idPedido, $idUsuario) {
            $pedido = $this->pedidoDelUsuario($idPedido, $idUsuario);
            $pago = $pedido->pago;

            if (! $pago || (int) $pago->Id_MetodoPago !== PedidoService::METODO_TRANSFERENCIA) {
                throw ValidationException::withMessages([
                    'boleta' => 'Este pedido no está asociado a transferencia bancaria.',
                ]);
            }

            if ((int) $pago->Id_Estatus !== EstatusCatalog::PAGO_PENDIENTE_COMPROBANTE) {
                throw ValidationException::withMessages([
                    'boleta' => 'El comprobante ya fue enviado o el pago no admite otra carga en este estado.',
                ]);
            }

            $extension = strtolower($archivo->getClientOriginalExtension());
            $nombreArchivo = Str::uuid()->toString().'.'.$extension;

            $boletaExistente = BoletaPago::query()
                ->where('Id_Pedido', $pedido->Id_Pedido)
                ->first();

            if ($boletaExistente && $boletaExistente->BoletaImagen) {
                Storage::disk('public')->delete($boletaExistente->BoletaImagen);
            }

            $archivo->storeAs(
                'boletas-pago/'.$pedido->Id_Pedido,
                $nombreArchivo,
                'public'
            );

            $rutaRelativa = 'boletas-pago/'.$pedido->Id_Pedido.'/'.$nombreArchivo;

            $boleta = BoletaPago::updateOrCreate(
                ['Id_Pedido' => $pedido->Id_Pedido],
                ['BoletaImagen' => $rutaRelativa]
            );

            $transaccion = is_array($pago->Transaccion_Json) ? $pago->Transaccion_Json : [];
            $transaccion['estado'] = 'pendiente_verificacion';

            Pago::query()
                ->where('Id_Pedido', $pedido->Id_Pedido)
                ->update([
                    'Id_Estatus' => EstatusCatalog::PAGO_PENDIENTE_VERIFICACION,
                    'Transaccion_Json' => $transaccion,
                ]);

            PedidoHistorial::create([
                'Id_Pedido' => $pedido->Id_Pedido,
                'Id_Estatus' => EstatusCatalog::PEDIDO_PENDIENTE,
                'Comentario' => 'Comprobante de transferencia recibido. Pendiente de verificación.',
                'Fecha_Cambio' => now(),
            ]);

            return $boleta;
        });
    }
}
