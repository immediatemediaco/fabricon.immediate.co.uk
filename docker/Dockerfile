FROM ghcr.io/immediatemediaco/php:8.5-fpm as app

RUN set -eux; \
    apk --update add --no-cache postgresql-dev; \
    docker-php-ext-install pdo_pgsql;

ENV APP_ENV=prod

COPY ./composer.* ./symfony.lock ./

RUN set -xe; \
    composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction; \
    composer clear-cache;

COPY . ./

RUN composer dump-autoload -a; \
    composer run-script --no-dev post-install-cmd; \
    composer dump-env prod; \
    chmod -R 777 var;

COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint

RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php-fpm"]

FROM nginx:1.27-alpine as web

WORKDIR /var/www/html

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

COPY --from=app /var/www/html/public ./public
