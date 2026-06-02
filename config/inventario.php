<?php

return [
    /*
    | Unidades disponibles (Stock - Stock_Reservado) en o por debajo de este valor
    | se consideran "stock bajo" en listados y alertas admin.
    */
    'umbral_bajo_stock' => (int) env('INVENTARIO_UMBRAL_BAJO_STOCK', 5),
];
