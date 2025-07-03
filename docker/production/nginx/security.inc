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
# Se recomienda que CrowdSec maneje esto para una gestión más dinámica,
# pero se mantiene como una capa adicional si se desea.
# if ($http_user_agent ~* (SemrushBot|AhrefsBot|MJ12bot|DotBot|PetalBot|python-requests)) {
#     return 403;
# }

# Content Security Policy (CSP) - Ejemplo básico, ajustar según necesidades
# add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; object-src 'none'; frame-ancestors 'self';" always;

# Rate Limiting (ejemplo, ajustar según sea necesario)
# limit_req_zone $binary_remote_addr zone=mylimit:10m rate=5r/s; # 5 requests per second
# limit_req zone=mylimit burst=10 nodelay;
# limit_conn_zone $binary_remote_addr zone=myconnlimit:10m;
# limit_conn myconnlimit 10; # 10 concurrent connections per IP