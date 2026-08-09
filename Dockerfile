# syntax=docker/dockerfile:1.7

FROM php:8.4-cli AS php-build

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libsqlite3-dev unzip \
    && docker-php-ext-install mbstring pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

FROM php-build AS php-dependencies

WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --prefer-dist
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction

FROM node:22-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci
COPY . .
RUN npm run build

FROM php-build AS test

WORKDIR /app
COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --prefer-dist
COPY . .
COPY --from=frontend /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-interaction \
    && cp .env.example .env \
    && php artisan key:generate --force \
    && touch database/testing.sqlite \
    && APP_ENV=testing \
       DB_CONNECTION=sqlite \
       DB_DATABASE=/app/database/testing.sqlite \
       CACHE_STORE=array \
       SESSION_DRIVER=array \
       QUEUE_CONNECTION=sync \
       MAIL_MAILER=array \
       php artisan migrate:fresh --seed --force \
    && APP_ENV=testing \
       DB_CONNECTION=sqlite \
       DB_DATABASE=/app/database/testing.sqlite \
       CACHE_STORE=array \
       SESSION_DRIVER=array \
       QUEUE_CONNECTION=sync \
       MAIL_MAILER=array \
       composer test

FROM php:8.4-apache AS production

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libsqlite3-dev \
    && docker-php-ext-install mbstring pdo_sqlite \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=php-dependencies --chown=www-data:www-data /app/app ./app
COPY --from=php-dependencies --chown=www-data:www-data /app/bootstrap ./bootstrap
COPY --from=php-dependencies --chown=www-data:www-data /app/config ./config
COPY --from=php-dependencies --chown=www-data:www-data /app/database ./database
COPY --from=php-dependencies --chown=www-data:www-data /app/resources/views ./resources/views
COPY --from=php-dependencies --chown=www-data:www-data /app/routes ./routes
COPY --from=php-dependencies --chown=www-data:www-data /app/storage ./storage
COPY --from=php-dependencies --chown=www-data:www-data /app/vendor ./vendor
COPY --from=php-dependencies --chown=www-data:www-data /app/artisan /app/composer.json ./
COPY --from=frontend --chown=www-data:www-data /app/public ./public

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/hebs-entrypoint

RUN chmod +x /usr/local/bin/hebs-entrypoint \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache database

LABEL org.opencontainers.image.title="HEBS School Platform" \
      org.opencontainers.image.description="Hamro English Boarding School website and CMS"

ENTRYPOINT ["hebs-entrypoint"]
CMD ["apache2-foreground"]
