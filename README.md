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
- **Autenticación:** Laravel Sanctum
- **Bundling:** Vite

## 💻 Requisitos Previos

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/compose-file/compose-file-v1/)
- [GNU Make](https://www.gnu.org/software/make/)

## ⚙️ Instalación con Docker y Make

1. **Clonar el repositorio**
   ```bash
   git clone [url-del-repositorio]
   cd Perfumarte
   ```

2. **Configurar entorno**
   ```bash
   make
   ```

3. **Generar clave de aplicación**
   ```bash
   make app_key
   ```

4. **Compilación de assets**
   ```bash
   # Modo producción
   make compile
   
   # Modo desarrollo con hot-reload
   make dev
   ```

5. **Ejecutar el proyecto**
   ```bash
   # Entorno local por defecto (localhost:8000)
   make serve
   
   # Red personalizada (ejemplo)
   make serve host=0.0.0.0 port=8080
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