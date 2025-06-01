#!/bin/sh
set -e

# Asignar permisos adecuados
chown -R www-data:www-data /var/www

# Instalar dependencias
composer install --no-interaction --no-progress
npm install
npm run build

# Iniciar el servidor Reverb en segundo plano
php artisan reverb:start &

# Ejecutar el comando principal
exec "$@"