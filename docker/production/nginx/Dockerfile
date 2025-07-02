FROM nginx:1.25.4-alpine

# Copiar nuestro script de entrypoint personalizado a la imagen
# Asegúrate de que la ruta de origen sea correcta respecto al contexto de build
COPY ./docker/production/nginx/entrypoint.sh /usr/local/bin/nginx-entrypoint.sh

# Darle permisos de ejecución
RUN chmod +x /usr/local/bin/nginx-entrypoint.sh

# Establecer nuestro script como el punto de entrada
ENTRYPOINT ["/usr/local/bin/nginx-entrypoint.sh"]

# El CMD por defecto de la imagen base nginx (normalmente `nginx -g 'daemon off;'`)
# será pasado como argumento a nuestro ENTRYPOINT si no especificamos un CMD aquí.
# Nuestro entrypoint.sh ya maneja esto con `exec nginx -g 'daemon off;'` al final.
# Por lo tanto, no es estrictamente necesario un CMD aquí, pero podemos dejar el default.
CMD ["nginx", "-g", "daemon off;"]
