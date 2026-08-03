# 🌟 Perfumarte - Sistema de Gestión de Inventario

<div align="center">

![Perfumarte Logo](public/assets/images/Logo_1.avif)

*Elegancia y Sofisticación en la Gestión de Perfumería*

</div>

## 📋 Acerca del Proyecto

Perfumarte es un sistema de gestión de inventario diseñado específicamente para una prestigiosa perfumería ubicada en Colombia. Este software combina la elegancia en su diseño con la eficiencia en la gestión de inventario, proporcionando una solución completa para el control y seguimiento de productos de perfumería de alta gama.

### ✨ Características Principales

- 📦 Control detallado de inventario de perfumes y productos relacionados
- 🔄 Gestión de entradas y salidas de mercancía
- 📊 Reportes
- 👥 Sistema de gestión de usuarios y roles
- 📱 Interfaz responsive y moderna

## 🛠️ Tecnologías Utilizadas

- **Backend:** Laravel 10 (PHP 8.3+)
- **Frontend:** Vue.js 3 + Inertia.js
- **Estilos:** Bootstrap 5.3 + SASS
- **Base de Datos:** MySQL
- **Autenticación:** sesión de Laravel (Breeze). Sanctum está instalado pero el
  proyecto no emite tokens de API.
- **Bundling:** Vite

## 💻 Requisitos Previos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/compose-file/compose-file-v1/)
- [GNU Make](https://www.gnu.org/software/make/)

## ⚙️ Instalación desde cero

Siga los pasos en orden. **Ninguno es opcional**: sin el paso 4 la aplicación
arranca sin tablas, y sin el paso 5 no existe ningún permiso en la base de datos
y ningún usuario puede acceder a ningún módulo.

1. **Clonar el repositorio y preparar el entorno**
   ```bash
   git clone [url-del-repositorio]
   cd Perfumarte
   cp .env.example .env
   ```

   Edite `.env` y configure al menos las credenciales de base de datos
   (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). En producción,
   además: `APP_ENV=production`, `APP_DEBUG=false` y
   `SESSION_SECURE_COOKIE=true`.

2. **Levantar los contenedores**
   ```bash
   make
   ```

3. **Generar la clave de aplicación**
   ```bash
   make app_key
   ```

4. **Crear el esquema de base de datos**
   ```bash
   make migrate
   ```

5. **Cargar roles, permisos y el usuario inicial**
   ```bash
   make seed
   ```

   El seeder crea los roles y permisos que las rutas comprueban, y una cuenta
   técnica inicial con rol `TI`. La contraseña temporal se imprime **una sola
   vez** en la salida del comando; anótela. Puede fijarla de antemano con
   `ADMIN_SEED_USERNAME` y `ADMIN_SEED_PASSWORD` en el `.env`.

   La cuenta nace marcada con contraseña predeterminada: el sistema obliga a
   cambiarla en el primer ingreso.

6. **Compilar los assets**
   ```bash
   # Modo producción
   make compile

   # Modo desarrollo con hot-reload
   make dev
   ```

7. **Ejecutar el proyecto**
   ```bash
   # Entorno local por defecto (localhost:8000)
   make serve

   # Red personalizada (ejemplo)
   make serve host=0.0.0.0 port=8080
   ```

### Verificar que la instalación quedó correcta

```bash
make cli command="php artisan migrate:status"   # todas las migraciones en Ran
make cli command="php artisan tinker --execute='echo Spatie\\Permission\\Models\\Permission::count();'"
```

El segundo comando debe devolver un número mayor que cero. Si devuelve `0`, el
paso 5 no se ejecutó y la aplicación responderá 403 en todos los módulos.

### Reconstruir la base en desarrollo

```bash
make fresh    # DESTRUCTIVO: borra y recrea todo el esquema, luego siembra
```

## 🚀 Uso en Producción

1. **Construir con la etapa de producción**
   ```bash
   APP_ENV=production make up
   ```

   Esto construye la etapa `production` del `Dockerfile`, que instala las
   dependencias con `--no-dev`, aplica el `php.ini` de producción y habilita
   OPcache.

2. **Aplicar migraciones**
   ```bash
   APP_ENV=production make migrate
   ```

3. **Optimizar la aplicación**
   ```bash
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

   > La caché de rutas es un archivo estático: **hay que regenerarla en cada
   > despliegue que toque `routes/`**, o la aplicación seguirá sirviendo el mapa
   > anterior.

4. **Asegurar permisos**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

## 🧪 Pruebas y estilo

```bash
make test     # suite de pruebas (Pest)
make lint     # formateo (Laravel Pint)
```

Las pruebas `Feature` usan `RefreshDatabase` y requieren una base MySQL
accesible; se ejecutan dentro del contenedor.

## 🔒 Seguridad

- Autenticación por sesión con contraseñas hasheadas con bcrypt.
- Roles y permisos con `spatie/laravel-permission`, versionados en
  `database/seeders/RolePermissionSeeder.php`.
- Protección CSRF y validación de entrada en los controladores.
- Las cuentas nuevas, reactivadas o con contraseña restablecida se activan
  mediante un **enlace firmado con vencimiento** que emite el administrador y
  entrega al titular por un canal seguro. El enlace se muestra una sola vez tras
  la operación.

### Gestión de contraseñas

El sistema no dispone de canal de correo: la tabla `users` no tiene columna
`email`. Por eso la entrega de credenciales es responsabilidad del
administrador:

1. El administrador crea, reactiva o restablece la cuenta.
2. La interfaz muestra un enlace de activación válido 72 horas.
3. El administrador se lo entrega al titular.
4. El titular fija su contraseña; el enlace deja de servir.

Un usuario que ya conoce su contraseña puede cambiarla desde
`/change-password`, donde se le exige la contraseña actual.

## 👥 Equipo

Desarrollado con ❤️ para Perfumarte Colombia.

## 📄 Licencia

Este software es propietario y está protegido por derechos de autor. © 2024 Perfumarte Colombia.