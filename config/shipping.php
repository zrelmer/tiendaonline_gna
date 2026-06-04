<?php

return [

    'umbral_envio_gratis' => (float) env('SHIPPING_FREE_THRESHOLD', 300),

    'costo_envio' => (float) env('SHIPPING_COST', 35),

    /*
    | Slugs de categoría sin costo de envío (entrega digital, Q 0).
    | El slug exacto "licencia" aplica a toda categoría con ese identificador.
    */
    'categorias_digitales_slug' => [
        'licencia',
        'licencias-antivirus',
        'licencias-de-antivirus',
        'licencias-office',
        'licencias-de-office',
        'licencias-microsoft-office',
        'productos-digitales',
        'producto-digital',
        'streaming',
        'productos-streaming',
    ],

    /*
    | Si el nombre/slug/descripción de la categoría contiene alguna de estas
    | palabras, el producto se considera digital (sin costo de envío).
    */
    'palabras_clave_digitales' => [
        'licencia antivirus',
        'licencias antivirus',
        'licencia office',
        'licencias office',
        'microsoft office',
        'producto digital',
        'productos digitales',
        'streaming',
        'entrega digital',
        'descarga digital',
    ],

];
