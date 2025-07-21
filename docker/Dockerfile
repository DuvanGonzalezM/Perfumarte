# Stage 1: Builder
# compila el código de la aplicación y los assets de frontend.
FROM php:8.3-fpm as builder

# Instalar dependencias del sistema para compilar
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pcntl pdo_mysql gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar archivos de dependencias e instalar
COPY database ./database
COPY composer.json composer.lock ./ 
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist

COPY package.json package-lock.json ./ 
RUN npm install

# Copiar el script de entrada y darle permisos de ejecución
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copiar el resto de la aplicación y construir assets
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN npm run build

# Limpiar artefactos de build de node
RUN rm -rf node_modules

# ----------------------------------------------------------------------------

# Stage 2: Final Image
FROM php:8.3-fpm

# Instalar solo las extensiones de PHP necesarias para producción
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip

WORKDIR /var/www

# Copiar la aplicación compilada desde la etapa 'builder'
COPY --from=builder /var/www . 

ARG APP_ENV=local
ENV APP_ENV=${APP_ENV}
# Copiar la configuración de Supervisor para producción
COPY docker/${APP_ENV}/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configurar PHP para producción
COPY docker/${APP_ENV}/php/php.ini /usr/local/etc/php/conf.d/app.ini

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

# Iniciar Supervisor para que gestione todos los procesos
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]