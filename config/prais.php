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

    /*
    |--------------------------------------------------------------------------
    | Jerarquía de usuarios
    |--------------------------------------------------------------------------
    |
    | Se declara por nombre de rol, no por id: los ids dependen del orden de
    | inserción del RolePermissionSeeder y cambian al reconstruir la base.
    |
    */

    'hierarchy' => [

        // Rol => rol que debe tener su jefe. Un rol que no aparezca aquí no
        // lleva jefe y el formulario no pide el campo.
        'boss_role' => [
            'Supervisor' => 'Subdirector',
            'Asesor comercial' => 'Supervisor',
            'Usuario' => 'Supervisor',
        ],

        // Roles a los que se les pregunta si operarán la caja.
        'cash_register_roles' => [
            'Asesor comercial',
        ],

        // Roles que se asignan a una zona en lugar de a una sede.
        'zone_roles' => [
            'Auxiliar administrativo',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Productos con rol operativo
    |--------------------------------------------------------------------------
    |
    | Productos que la aplicación necesita reconocer para operar: los envases
    | que ofrece la venta, la bolsa de regalo y las materias primas del
    | laboratorio. Se identifican por la columna products.operational_role, no
    | por id: el catálogo se recarga y los ids cambian.
    |
    | La clave es el valor guardado; el texto es lo que ve el administrador al
    | crear o editar un producto.
    |
    */

    'product_roles' => [

        'labels' => [
            'container_30' => 'Envase de 30 ml',
            'container_50' => 'Envase de 50 ml',
            'container_100' => 'Envase de 100 ml',
            'gift_bag' => 'Bolsa de regalo',
            'solvent' => 'Disolvente (laboratorio)',
            'dipropylene' => 'Dipropileno (laboratorio)',
        ],

        // Roles que solo puede tener un producto a la vez. Los envases no están
        // aquí: cada presentación admite varias referencias.
        'exclusive' => [
            'gift_bag',
            'solvent',
            'dipropylene',
        ],

        // Presentación en ml => rol que agrupa sus envases. Lo consume el
        // formulario de venta para saber qué envases ofrecer por tamaño.
        'containers_by_size' => [
            30 => 'container_30',
            50 => 'container_50',
            100 => 'container_100',
        ],
    ],

];
