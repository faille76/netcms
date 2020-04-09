FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl debconf subversion apt-transport-https apt-utils \
    build-essential locales acl mailutils wget zip unzip zlib1g-dev libicu-dev libmagickwand-dev

RUN docker-php-ext-configure intl
RUN docker-php-ext-install opcache pdo pdo_mysql intl

RUN docker-php-ext-configure opcache --enable-opcache

RUN pecl install imagick && docker-php-ext-enable imagick
RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /usr/local/etc/php/php.ini
COPY symfony.ini /etc/php/7.4/conf.d/
COPY symfony.ini /etc/php/7.4/cli/conf.d/
COPY xdebug.ini  /etc/php/7.4/conf.d/

COPY symfony.pool.conf /etc/php/7.4/php-fpm.d/

CMD ["php-fpm"]

WORKDIR /var/www/symfony
EXPOSE 9000