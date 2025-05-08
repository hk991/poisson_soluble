FROM php:8.3-fpm-alpine

RUN apk update && apk upgrade && apk add --no-cache autoconf gcc g++ make libc-dev libpq-dev pkgconf re2c file && \
    rm -rf /var/cache/apk/* && \
    docker-php-ext-install mysqli pdo pdo_mysql pdo_pgsql

COPY . /usr/src/app

WORKDIR /usr/src/app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/bin/composer && \
    chmod +x /usr/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom_php.ini

RUN composer install && \
    php bin/console cache:clear --no-optional-warmers && \
    php bin/console asset:install && \
    rm -rf var/log/*.log var/cache/* && \
    mkdir -p var/cache var/log var/sessions && \
    chmod -R 770 var/cache var/log var/sessions && \
    rm -rf /root/.composer /tmp/* /var/cache/* /home/*/.cache

COPY docker/php/init-db.sh /usr/local/bin/init-db.sh
RUN chmod +x /usr/local/bin/init-db.sh
ENTRYPOINT ["/usr/local/bin/init-db.sh"]