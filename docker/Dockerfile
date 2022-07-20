FROM ghcr.io/immediate-media/php:8.1-fpm

RUN set -eux; \
    apk --update add --no-cache postgresql-dev; \
    docker-php-ext-install pdo_pgsql;

ENV APP_ENV=prod COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2.3 /usr/bin/composer /usr/bin/composer

COPY ./composer.* ./symfony.lock ./

RUN set -xe; \
    composer install --prefer-dist --no-dev --no-scripts --no-progress --ignore-platform-reqs --no-interaction; \
    composer clear-cache;

COPY . ./

RUN composer dump-autoload --classmap-authoritative; \
    composer run-script --no-dev post-install-cmd; \
    composer dump-env prod; \
    chmod -R 777 var;

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
