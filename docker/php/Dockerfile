FROM ubuntu:18.04

RUN apt-get update

RUN DEBIAN_FRONTEND="noninteractive" apt-get -y install tzdata

RUN apt-get install -my \
  curl \
  wget \
  php-curl \
  php-fpm \
  php-gd \
  php-xsl \
  php-mysqlnd \
  php-xdebug \
  php-cli \
  php-intl \
  php-bz2 \
  php-zip \
  php-mbstring \
  git \
  zip \
  php-apcu \
  php-redis \
  php-opcache

RUN mkdir /run/php
ADD conf/www.conf /etc/php/7.2/fpm/pool.d/www.conf
ADD conf/xdebug.ini /etc/php/7.2/mods-available/xdebug.ini
ADD conf/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer
RUN ln -snf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

WORKDIR /var/www/project
EXPOSE 8000

CMD ["php-fpm7.2"]