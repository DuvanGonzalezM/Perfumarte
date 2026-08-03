<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Arranque inicial (php artisan prais:bootstrap)
    |--------------------------------------------------------------------------
    |
    | Parámetros de la cuenta técnica que se crea una sola vez sobre una base
    | de datos nueva. Se leen por config (no con env() en tiempo de ejecución)
    | para que sigan funcionando cuando la configuración está cacheada con
    | `php artisan config:cache`.
    |
    */

    'bootstrap' => [

        // Nombre de usuario de la cuenta TI inicial.
        'username' => env('PRAIS_ADMIN_USERNAME', 'ti'),

        // Nombre visible de la cuenta.
        'name' => env('PRAIS_ADMIN_NAME', 'Administrador técnico'),

        // Contraseña inicial. Dejar VACÍA es lo recomendado: el comando la pide
        // por teclado sin mostrarla en pantalla. Solo se usa con --from-env
        // (despliegues desatendidos) y el comando la borra del .env al terminar.
        'password' => env('PRAIS_ADMIN_PASSWORD'),

        // Longitud mínima exigida a la contraseña de la cuenta TI inicial.
        // Es más estricta que la del resto de la aplicación por ser una cuenta
        // con todos los permisos.
        'min_password_length' => (int) env('PRAIS_ADMIN_MIN_PASSWORD_LENGTH', 14),

        // Vigencia del enlace de activación firmado que emite --link.
        'activation_ttl_hours' => (int) env('PRAIS_ACTIVATION_TTL_HOURS', 72),
    ],

];
