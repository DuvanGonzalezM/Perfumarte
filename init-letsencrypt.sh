#!/bin/bash

# Este script se debe ejecutar manualmente la primera vez para obtener el certificado SSL.
#
# Uso: ./init-letsencrypt.sh

# Espera a que Nginx esté en funcionamiento
echo "### Esperando a que Nginx esté en funcionamiento..."
while ! docker-compose exec webserver nginx -t; do
  sleep 5
done

echo "### Nginx está en funcionamiento, solicitando certificado SSL..."

# Solicita el certificado de Let's Encrypt
docker-compose run --rm --entrypoint "\
  certbot certonly --webroot --webroot-path=/var/www/certbot \
    --email jjorozco@unimonserrate.edu.co \
    -d www.prais.perfum-arte.com \
    --rsa-key-size 4096 \
    --agree-tos \
    --non-interactive \
    --force-renewal" certbot

# Recarga Nginx para aplicar los cambios en la configuración SSL
docker-compose exec webserver nginx -s reload
