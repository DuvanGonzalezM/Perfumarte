# 🌟 Perfumarte - Sistema de Gestión de Inventario

<div align="center">

![Perfumarte Logo](public/assets/images/Logo_1.avif)

*Elegancia y Sofisticación en la Gestión de Perfumería*

</div>

## 📋 Acerca del Proyecto

Perfumarte (PRAIS) es un sistema de gestión de inventario diseñado específicamente para una
prestigiosa perfumería ubicada en Colombia. Este software combina la elegancia en su diseño
con la eficiencia en la gestión de inventario, proporcionando una solución completa para el
control y seguimiento de productos de perfumería de alta gama.

### ✨ Características Principales

- 📦 Control detallado de inventario de perfumes y productos relacionados
- 🔄 Gestión de entradas y salidas de mercancía (despachos, solicitudes, devoluciones)
- 🧪 Transformaciones y reenvases de laboratorio
- 💰 Ventas, caja y arqueo por sede
- 🔍 Auditorías de inventario y de caja
- 📊 Reportes
- 👥 Sistema de gestión de usuarios, roles y permisos
- 📱 Interfaz responsive y moderna

## 🛠️ Tecnologías Utilizadas

- **Backend:** Laravel 10 (PHP 8.3+)
- **Frontend:** Vue.js 3 + Inertia.js
- **Estilos:** Bootstrap 5.3 + SASS
- **Base de Datos:** MySQL 8
- **Autenticación:** sesión de Laravel (Breeze). Sanctum está instalado pero el
  proyecto no emite tokens de API.
- **Tiempo real:** Laravel Reverb
- **Bundling:** Vite

## 💻 Requisitos Previos

- [Docker](https://www.docker.com/get-started) y [Docker Compose](https://docs.docker.com/compose/)
- [GNU Make](https://www.gnu.org/software/make/)
- **Un servidor MySQL 8 accesible con una base de datos vacía.** El proyecto **no**
  levanta la base de datos: los contenedores son la aplicación (php-fpm), nginx y
  Reverb. Por defecto la aplicación busca el motor en `host.docker.internal`.

Si la base todavía no existe, créela con un usuario propio para la aplicación —no use
`root` en producción—:

```sql
CREATE DATABASE perfumarte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'perfumarte'@'%' IDENTIFIED BY 'una-contraseña-larga-y-aleatoria';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES
  ON perfumarte.* TO 'perfumarte'@'%';
FLUSH PRIVILEGES;
```

## ⚙️ Levantamiento sobre una base de datos nueva

Todo el arranque de datos ocurre en **un solo comando**: `make bootstrap`. Crea el
esquema, carga roles y permisos, y provisiona la cuenta técnica inicial con rol `TI`,
que es la única capaz de crear al resto de usuarios.

1. **Clonar el repositorio y preparar el entorno**

   ```bash
   git clone [url-del-repositorio]
   cd Perfumarte
   cp .env.example .env
   ```

   Edite `.env`:

   | Variable | Qué poner |
   |---|---|
   | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Credenciales de la base vacía del paso anterior |
   | `APP_URL` | URL real del sitio; de ella salen los enlaces de activación firmados |
   | `PRAIS_ADMIN_USERNAME` | Nombre de usuario de la cuenta TI inicial (por defecto `ti`) |
   | `PRAIS_ADMIN_PASSWORD` | **Déjela vacía.** El comando pedirá la contraseña por teclado |

   En producción, además: `APP_ENV=production`, `APP_DEBUG=false` y
   `SESSION_SECURE_COOKIE=true`.

2. **Levantar los contenedores**

   ```bash
   make
   ```

3. **Generar la clave de aplicación**

   ```bash
   make app_key
   ```

   Sin `APP_KEY` no hay cifrado de sesión y el arranque se detiene con un error explícito.

4. **Arrancar el aplicativo sobre la base nueva**

   ```bash
   make bootstrap
   ```

   El comando pide la contraseña de la cuenta TI **sin mostrarla en pantalla** y la
   solicita dos veces. No se escribe en ningún archivo, en la salida del comando ni en
   los logs.

5. **Compilar los assets**

   ```bash
   make compile   # producción
   make dev       # desarrollo con hot-reload
   ```

6. **Ejecutar el proyecto**

   ```bash
   make serve                          # localhost:8000
   make serve host=0.0.0.0 port=8080   # red personalizada
   ```

7. **Primer ingreso**

   Entre con el usuario TI. El sistema le exigirá cambiar la contraseña antes de
   permitirle abrir cualquier módulo, y desde *Administración → Usuarios* podrá crear las
   demás cuentas.

### Qué hace exactamente `make bootstrap`

| Paso | Efecto | Si ya está hecho |
|---|---|---|
| Verificación previa | `APP_KEY` presente, conexión a la base, y en producción `APP_DEBUG=false` | — |
| `migrate --force` | Crea el esquema completo (36 tablas) | No repite migraciones aplicadas |
| `RolePermissionSeeder` | 55 permisos y 12 roles que las rutas comprueban | Los refresca sin duplicar |
| Cuenta TI | Crea la cuenta con rol `TI` | **No la toca**: avisa y termina bien |

Es seguro repetirlo en cada despliegue: nunca crea una segunda cuenta TI ni pisa la
existente.

### Modos del comando

```bash
# Interactivo (recomendado): pide la contraseña por teclado
make bootstrap

# El titular fija su propia contraseña: emite un enlace de activación firmado
make bootstrap opts="--link"

# Desatendido (CI/CD): toma la contraseña de PRAIS_ADMIN_PASSWORD y la borra del .env
make bootstrap opts="--from-env"

# Otro nombre de usuario para la cuenta técnica
make bootstrap opts="--username=ti-perfumarte --name='Soporte técnico'"
```

Opciones adicionales: `--skip-migrations` (solo roles y cuenta), `--keep-env` (conserva
`PRAIS_ADMIN_PASSWORD` en el `.env`), `--force` (no pide confirmación en producción).
La lista completa está en `make cli command="php artisan prais:bootstrap --help"`.

Con `--link` el comando imprime una URL firmada, válida 72 horas y de un solo uso, con la
que el titular fija su contraseña. Hasta ese momento la cuenta no puede entrar a ningún
módulo. Entréguela por un canal seguro: no se vuelve a mostrar.

### Parámetros de seguridad del arranque

- **Contraseña de la cuenta TI:** mínimo 14 caracteres, mayúsculas y minúsculas, números y
  símbolos; no puede contener el nombre de usuario y se contrasta contra la lista pública
  de contraseñas filtradas (consulta k-anonymity: la contraseña nunca sale del servidor).
  Ajustable con `PRAIS_ADMIN_MIN_PASSWORD_LENGTH`.
- **Sin secretos en disco ni en logs:** la contraseña no se imprime nunca. Si se usó
  `--from-env`, el comando vacía `PRAIS_ADMIN_PASSWORD` en el `.env` al terminar.
- **Cambio obligatorio:** salvo que el propio titular la haya escrito en la terminal, la
  cuenta nace con `default_password = true` y el middleware `CheckFirstLogin` la retiene
  en la pantalla de cambio de contraseña.
- **Una sola vez:** si ya existe una cuenta con rol `TI`, el comando no crea otra. No es
  una vía para fabricarse un administrador en un sistema en marcha.
- **Producción protegida:** aborta con `APP_DEBUG=true`, avisa si
  `SESSION_SECURE_COOKIE=false` o si `APP_URL` apunta a `localhost`, y pide confirmación
  explícita antes de tocar producción.

### Verificar que la instalación quedó correcta

```bash
make cli command="php artisan migrate:status"   # todas las migraciones en Ran
make cli command="php artisan prais:bootstrap"  # debe responder que la cuenta TI ya existe
```

Si el segundo comando ofrece crear una cuenta TI, el paso 4 no se completó y la
aplicación responderá 403 en todos los módulos.

### Reconstruir la base en desarrollo

```bash
make fresh    # DESTRUCTIVO: borra y recrea todo el esquema, luego siembra
```

`make fresh` y `make seed` usan `AdminUserSeeder`, que aplica las mismas reglas que el
comando de arranque: nunca imprime una contraseña. Fuera de producción acepta la de
`PRAIS_ADMIN_PASSWORD` si cumple la política; en caso contrario emite el enlace de
activación.

## 🚀 Uso en Producción

1. **Construir con la etapa de producción**

   ```bash
   APP_ENV=production make up
   ```

   Esto construye la etapa `production` del `Dockerfile`, que instala las dependencias con
   `--no-dev`, aplica el `php.ini` de producción y habilita OPcache.

2. **Arrancar o actualizar la base**

   ```bash
   APP_ENV=production make bootstrap   # primer despliegue: esquema + roles + cuenta TI
   APP_ENV=production make migrate     # despliegues posteriores
   ```

3. **Optimizar la aplicación**

   ```bash
   make cache_clear   # config:clear, route:cache, view:cache, optimize
   ```

   > La caché de rutas es un archivo estático: **hay que regenerarla en cada despliegue
   > que toque `routes/`**, o la aplicación seguirá sirviendo el mapa anterior. Lo mismo
   > con `config:cache` y los cambios de `.env`.

4. **Asegurar permisos**

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### Checklist antes de exponer el sitio

- [ ] `APP_ENV=production` y `APP_DEBUG=false`
- [ ] `APP_KEY` generada y respaldada (perderla invalida las sesiones y los datos cifrados)
- [ ] `SESSION_SECURE_COOKIE=true` y HTTPS forzado en nginx
- [ ] `APP_URL` con el dominio real (de ahí salen los enlaces firmados)
- [ ] Usuario de base de datos propio, sin privilegios administrativos
- [ ] `PRAIS_ADMIN_PASSWORD` vacía en el `.env` del servidor
- [ ] `CORS_ALLOWED_ORIGINS` y `REVERB_ALLOWED_ORIGINS` con el dominio real
- [ ] `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` configuradas (el login las usa)
- [ ] Certificados reales en `docker/production/ssl/` (los `*_example.pem` son plantillas)
- [ ] Respaldo de la base de datos programado

## 🧪 Pruebas y estilo

```bash
make test     # suite de pruebas (Pest)
make lint     # formateo (Laravel Pint)
```

Las pruebas `Feature` usan `RefreshDatabase` y requieren una base MySQL accesible; se
ejecutan dentro del contenedor.

Las pruebas que renderizan una página Inertia necesitan el manifiesto de Vite, que genera
`make compile`. Si no está presente, esas pruebas se omiten con un mensaje explícito en
lugar de fallar.

## 🔒 Seguridad

- Autenticación por sesión con contraseñas hasheadas con bcrypt.
- Roles y permisos con `spatie/laravel-permission`, versionados en
  `database/seeders/RolePermissionSeeder.php`.
- Protección CSRF, reCAPTCHA v3 en el login y validación de entrada en los controladores.
- Alcance por sede y zona: los módulos filtran por `location_id` / `zone_id` del usuario.
- Las cuentas nuevas, reactivadas o con contraseña restablecida se activan mediante un
  **enlace firmado con vencimiento** que emite el administrador y entrega al titular por un
  canal seguro. El enlace se muestra una sola vez tras la operación.
- La cuenta técnica inicial se crea con el comando de arranque bajo las reglas descritas en
  [Parámetros de seguridad del arranque](#parámetros-de-seguridad-del-arranque).

### Gestión de contraseñas

El sistema no dispone de canal de correo: la tabla `users` no tiene columna `email`. Por
eso la entrega de credenciales es responsabilidad del administrador:

1. El administrador crea, reactiva o restablece la cuenta.
2. La interfaz muestra un enlace de activación válido 72 horas.
3. El administrador se lo entrega al titular.
4. El titular fija su contraseña; el enlace deja de servir.

Un usuario que ya conoce su contraseña puede cambiarla desde `/change-password`, donde se
le exige la contraseña actual.

### Si se pierde el acceso a la cuenta TI

El comando de arranque no sirve para recuperarla: solo actúa cuando no existe ninguna
cuenta TI. Restablézcala desde otra cuenta con permiso *Editar Usuarios* (la interfaz
emite un enlace de activación), o, si no queda ninguna, desde el servidor:

```bash
make cli command="php artisan tinker"
>>> $u = App\Models\User::where('username', 'ti')->first();
>>> $u->update(['password' => Hash::make('temporal-larga-y-aleatoria'), 'default_password' => true, 'enabled' => true]);
```

El sistema exigirá cambiarla en el siguiente ingreso.

## 👥 Equipo

Desarrollado con ❤️ para Perfumarte Colombia.

## 📄 Licencia

Este software es propietario y está protegido por derechos de autor. © 2024 Perfumarte Colombia.
