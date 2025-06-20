# Bloquea el acceso a archivos sensibles que nunca deberían ser públicos.
location ~ /\. {
    deny all;
}

# Bloquea el acceso a archivos de configuración y otros archivos comunes de desarrollo.
location ~* /(?:composer\.(?:json|lock)|Dockerfile|docker-compose\.yml|\.env|\.env\.example|\.git) {
    deny all;
}

# Bloquea peticiones de bots que buscan vulnerabilidades comunes (ej. WordPress).
# Usamos 'deny all' para que genere un error 403 que quede registrado en los logs.
location ~* /(?:wp-admin|wp-login\.php|xmlrpc\.php) {
    deny all;
}

# Bloquea user-agents de bots de spam y scrapers conocidos.
if ($http_user_agent ~* (SemrushBot|AhrefsBot|MJ12bot|DotBot|PetalBot|python-requests)) {
    return 403;
}