FROM php:7.3-fpm-alpine

# Timezone
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN printf "[PHP]\ndate.timezone = \"$TZ\"\n" > /usr/local/etc/php/conf.d/tzone.ini

# Install composer
RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Optimize composer time
RUN composer global require hirak/prestissimo --no-plugins --no-scripts

RUN mkdir -p /php
WORKDIR /php

COPY ./. /php/
