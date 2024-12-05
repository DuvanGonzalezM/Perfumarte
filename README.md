# 🌟 Perfumarte - Sistema de Gestión de Inventario

<div align="center">

![Perfumarte Logo](public/images/logo.png)

*Elegancia y Sofisticación en la Gestión de Perfumería*

</div>

## 📋 Acerca del Proyecto

Perfumarte es un sistema de gestión de inventario diseñado específicamente para una prestigiosa perfumería ubicada en Colombia. Este software combina la elegancia en su diseño con la eficiencia en la gestión de inventario, proporcionando una solución completa para el control y seguimiento de productos de perfumería de alta gama.

### ✨ Características Principales

- 📦 Control detallado de inventario de perfumes y productos relacionados
- 🔄 Gestión de entradas y salidas de mercancía
- 📊 Reportes y estadísticas avanzadas
- 👥 Sistema de gestión de usuarios y roles
- 🏷️ Control de precios y promociones
- 📱 Interfaz responsive y moderna

## 🛠️ Tecnologías Utilizadas

- **Backend:** Laravel 10 (PHP 8.1+)
- **Frontend:** Vue.js 3 + Inertia.js
- **Estilos:** Bootstrap 5.3 + SASS
- **Base de Datos:** MySQL
- **Autenticación:** Laravel Sanctum
- **Bundling:** Vite

## 💻 Requisitos del Sistema

- PHP >= 8.1
- Composer
- Node.js >= 16.x
- MySQL >= 8.0

## ⚙️ Instalación

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd Perfumarte
   ```

2. **Configurar el entorno**
   ```bash
   cp .env.example .env
   # Configurar las variables de entorno en el archivo .env
   ```

3. **Instalar dependencias**
   ```bash
   composer install
   npm install
   ```

4. **Generar clave de aplicación**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones**
   ```bash
   php artisan migrate --seed
   ```

6. **Compilar assets**
   ```bash
   # Para desarrollo
   npm run dev

   # Para producción
   npm run build
   ```

## 🚀 Uso en Producción

1. **Optimizar la aplicación**
   ```bash
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Asegurar permisos**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

## 🔒 Seguridad

Este sistema implementa múltiples capas de seguridad:
- Autenticación robusta con Laravel Sanctum
- Sistema de roles y permisos
- Protección contra CSRF
- Validación de datos en todas las entradas

## 👥 Equipo

Desarrollado con ❤️ para Perfumarte Colombia.

## 📄 Licencia

Este software es propietario y está protegido por derechos de autor. © 2024 Perfumarte Colombia.