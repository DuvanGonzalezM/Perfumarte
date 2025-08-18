#!/bin/sh
set -e

# Asignar permisos adecuados
chown -R www-data:www-data /var/www

# Instalar dependencias
composer install --no-interaction --no-progress
npm install
npm run build

# Ejecutar el comando principal
exec "$@"