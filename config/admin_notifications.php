<?php

return [
    /*
    | Notificaciones por correo al equipo administrativo (no al cliente).
    | Lista separada por comas en ADMIN_NOTIFICATION_EMAILS.
    */
    'enabled' => env('ADMIN_NOTIFICATIONS_ENABLED', true),

    'emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ADMIN_NOTIFICATION_EMAILS', 'elmer.yatteni@gnacore.com.gt,karla.caprielh@gnacore.com.gt'))
    ))),

    'from_name' => env('ADMIN_NOTIFICATION_FROM_NAME', env('MAIL_FROM_NAME', 'GNA Core Tienda')),
];
