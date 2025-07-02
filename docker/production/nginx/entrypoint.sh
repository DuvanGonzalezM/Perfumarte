#!/bin/sh
set -e

LOG_DIR="/var/log/nginx"
ACCESS_LOG="$LOG_DIR/access.log"
ERROR_LOG="$LOG_DIR/error.log"

# Asegurarse de que el directorio de logs exista
mkdir -p "$LOG_DIR"
# No es estrictamente necesario cambiar el propietario del directorio si los archivos dentro tienen el propietario correcto,
# pero puede ser una buena práctica. Nginx crea este directorio si no existe con el usuario maestro (root).
# Si el directorio ya existe y es propiedad de root, y Nginx corre como nginx, necesita permisos de escritura en el directorio
# o que los archivos de log ya existan y sean escribibles por el usuario nginx.
# Para simplificar y asegurar, nos enfocamos en los archivos.

# Eliminar symlinks si existen y crear archivos
echo "Verificando y preparando $ACCESS_LOG..."
if [ -L "$ACCESS_LOG" ]; then
    echo "$ACCESS_LOG es un symlink, eliminándolo."
    rm -f "$ACCESS_LOG"
fi
touch "$ACCESS_LOG"

echo "Verificando y preparando $ERROR_LOG..."
if [ -L "$ERROR_LOG" ]; then
    echo "$ERROR_LOG es un symlink, eliminándolo."
    rm -f "$ERROR_LOG"
fi
touch "$ERROR_LOG"

# Asegurar permisos correctos para los archivos de log
echo "Estableciendo permisos para los archivos de log..."
chown nginx:nginx "$ACCESS_LOG" "$ERROR_LOG"
chmod 640 "$ACCESS_LOG" "$ERROR_LOG"

echo "Archivos de log preparados en $LOG_DIR:"
ls -la "$LOG_DIR"

echo "Iniciando Nginx..."
# Ejecutar el comando Nginx original
exec nginx -g 'daemon off;'
