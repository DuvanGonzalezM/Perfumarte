#!/bin/bash

if !-f "/etc/letsencrypt/live/www.prais.perfum-arte.com/fullchain.pem"; then
  echo "### Solicitando certificado SSL para www.prais.perfum-arte.com..."

  # Inicia el proceso de solicitud del certificado en modo de prueba (staging)
  certbot certonly --webroot --webroot-path=/var/www/certbot \
    --email jjorozco@unimonserrate.edu.co \
    -d www.prais.perfum-arte.com \
    --rsa-key-size 4096 \
    --agree-tos \
    --non-interactive \
    --staging

  # Verifica si el certificado se obtuvo correctamente
  if [ $? -ne 0 ]; then
    echo "ERROR: No se pudo obtener el certificado de prueba de Let's Encrypt."
    exit 1
  fi

  echo "### Certificado de prueba obtenido, procediendo con el certificado de producción..."
  # Solicita el certificado de producción
  certbot certonly --webroot --webroot-path=/var/www/certbot \
    --email jjorozco@unimonserrate.edu.co \
    -d www.prais.perfum-arte.com \
    --rsa-key-size 4096 \
    --agree-tos \
    --non-interactive \
    --force-renewal
else
  echo "### El certificado para www.prais.perfum-arte.com ya existe."
fi

# Recarga Nginx para aplicar los cambios en la configuración SSL
docker-compose exec webserver nginx -s reload
