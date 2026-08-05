FROM php:8.2-cli

# Extensions nécessaires à Laravel + au driver Postgres utilisé en production.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache

# php artisan serve : choix pragmatique pour un déploiement de démo sur plan gratuit
# (pas de nginx/php-fpm à configurer) — pas un serveur multi-processus de production.
# $PORT est imposé par Render au démarrage du conteneur.
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
