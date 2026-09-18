FROM composer:2 AS composer

FROM php:8.4-apache AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libicu-dev \
        unzip \
        git \
    && docker-php-ext-install pdo_pgsql intl opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-interaction \
    && php bin/console assets:install public --no-interaction \
    && mkdir -p var/cache var/log public/uploads/trailers \
    && chown -R www-data:www-data var public/uploads

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
