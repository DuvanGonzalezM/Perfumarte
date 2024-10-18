# Perfumarte

Desarrollo del aplicativo para el inventario de la perfumería **Perfumarte**.

## Instalación

Para configurar y ejecutar este proyecto, sigue los siguientes pasos:

### 1. Solicitar el archivo `.env`

Antes de comenzar, debes solicitar el archivo `.env`, que contiene las configuraciones de entorno necesarias para el proyecto. Una vez recibido, coloca este archivo en la raíz del proyecto.

### 2. Instalar dependencias

Para instalar todas las dependencias de PHP y NodeJS, ejecuta el siguiente comando en la raíz del proyecto:

```bash
composer install
npm install
```

### 3. Compilación de activos

Dependiendo del entorno en el que te encuentres, deberás compilar los activos JavaScript y CSS.

- **Entorno local (desarrollo):**

Para entornos locales, ejecuta el siguiente comando para compilar y observar los archivos:

```bash
npm run dev
```

- **Entorno de producción:**

Para entornos de producción, donde necesitas compilar los activos optimizados, ejecuta el siguiente comando:

```bash
npm run build
```

### 4. Otros comandos útiles

Si es necesario, puedes ejecutar otros comandos como la limpieza de caché o migraciones de base de datos:

- **Limpieza de caché:**

```bash
php artisan cache:clear
```

## License

El framework Laravel es un software de código abierto licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).