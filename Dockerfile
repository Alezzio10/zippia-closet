# zippia-closet - Dockerfile para Railway
# Laravel 12 + PHP 8.4 + MySQL + Vite

FROM php:8.4-cli AS base

# Evitar preguntas interactivas en apt
ENV DEBIAN_FRONTEND=noninteractive

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        bcmath \
        intl \
        gd \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copiar dependencias
COPY composer.json composer.lock ./
COPY package.json package-lock.json* ./

# Instalar dependencias PHP
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar la aplicación (necesaria para npm run build - Vite lee resources/, vite.config, etc.)
COPY . .

# Completar autoload de Composer
RUN composer dump-autoload --optimize

# Instalar deps Node y compilar assets con Vite
RUN npm ci 2>/dev/null || npm install && npm run build

# Crear .env sin APP_KEY para que solo use la variable de entorno (Railway)
RUN if [ ! -f .env ]; then grep -v '^APP_KEY=' .env.example > .env || cp .env.example .env; fi

# Copiar y preparar entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Permisos para storage y bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

# Railway inyecta PORT
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
