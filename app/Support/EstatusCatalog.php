<?php

namespace App\Support;

/**
 * IDs de tb_estatus (sincronizados con EstatusSeeder).
 */
final class EstatusCatalog
{
    // Catálogo / producto
    public const PRODUCTO_ACTIVO = 1;

    public const PRODUCTO_INACTIVO = 2;

    public const PRODUCTO_AGOTADO = 3;

    public const PRODUCTO_PENDIENTE = 4;

    // Pago (tb_pago)
    public const PAGO_PENDIENTE_COMPROBANTE = 10;

    public const PAGO_PENDIENTE_VERIFICACION = 11;

    public const PAGO_PENDIENTE_COBRO = 12;

    public const PAGO_PAGADO = 13;

    public const PAGO_RECHAZADO = 14;

    // Pedido (tb_pedido)
    public const PEDIDO_PENDIENTE = 20;

    public const PEDIDO_CONFIRMADO = 21;

    public const PEDIDO_EN_PREPARACION = 22;

    public const PEDIDO_ENVIADO = 23;

    public const PEDIDO_ENTREGADO = 24;

    public const PEDIDO_CANCELADO = 25;

    // Envío (tb_envio)
    public const ENVIO_PENDIENTE = 30;

    public const ENVIO_EN_TRANSITO = 31;

    public const ENVIO_ENTREGADO = 32;
}
