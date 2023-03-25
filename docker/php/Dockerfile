FROM php:8.2.4-fpm

RUN ["apt-get", "update"]
RUN ["apt-get", "install", "-y", "zip", "unzip", "git", "vim"]

COPY php.ini /usr/local/etc/php/php.ini
COPY home/.bash_history /root/.bash_history
COPY home/.bashrc /root/.bashrc

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

RUN apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl

RUN ["apt-get", "install", "-y", "libpng-dev", "libpq-dev", "librabbitmq-dev", "libxslt1-dev", "libz-dev", "libzip-dev"]
RUN docker-php-ext-configure bcmath && docker-php-ext-install bcmath
RUN docker-php-ext-configure gd && docker-php-ext-install gd
RUN docker-php-ext-configure opcache && docker-php-ext-install opcache
RUN docker-php-ext-configure pdo_mysql && docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure pdo_pgsql && docker-php-ext-install pdo_pgsql
RUN docker-php-ext-configure xsl && docker-php-ext-install xsl
RUN docker-php-ext-configure zip && docker-php-ext-install zip

RUN pear config-set php_ini /usr/local/etc/php/php.ini && pecl install amqp && docker-php-ext-enable amqp
RUN pear config-set php_ini /usr/local/etc/php/php.ini && pecl install redis && docker-php-ext-enable redis

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN ["apt-get", "install", "-y", "symfony-cli"]

WORKDIR /var/www
ENV COMPOSER_ALLOW_SUPERUSER 1
