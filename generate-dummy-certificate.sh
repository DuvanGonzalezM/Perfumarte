#!/bin/bash

# Generar un certificado autofirmado para Nginx
#
# Uso: ./generate-dummy-certificate.sh

# Directorio de certificados de Let's Encrypt
LETSENCRYPT_DIR="/etc/letsencrypt/live/www.prais.perfum-arte.com"

# Verificar si ya existe un certificado
if [ -f "${LETSENCRYPT_DIR}/fullchain.pem" ]; then
  echo "El certificado ya existe en ${LETSENCRYPT_DIR}"
  exit 0
fi

echo "Generando un certificado autofirmado para Nginx..."

# Crear el directorio si no existe
mkdir -p "${LETSENCRYPT_DIR}"

# Generar el certificado y la clave privada
openssl req -x509 -nodes -newkey rsa:4096 -days 365 \
  -keyout "${LEDECRYPT_DIR}/privkey.pem" \
  -out "${LETSENCRYPT_DIR}/fullchain.pem" \
  -subj "/CN=www.prais.perfum-arte.com"

echo "Certificado autofirmado generado en ${LETSENCRYPT_DIR}"
