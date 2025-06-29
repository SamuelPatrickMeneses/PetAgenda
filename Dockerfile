FROM php:8.3.4-fpm

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-enable pdo_mysql
#RUN echo "deb http://ftp.br.debian.org/debian/" >> /etc/apt/source.list
RUN apt-get update -y && apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip
