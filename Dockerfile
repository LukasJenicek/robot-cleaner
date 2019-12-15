FROM php:7.3-alpine

ARG INPUT_FILE=resources/test1.json
ARG DESTINATION_FILE=resources/test1_output.json

RUN apk add --no-cache curl \
        bzip2 cyrus-sasl freetype gettext gmp icu libjpeg-turbo libpng \
        libwebp libxml2 libxslt libzip pcre zlib \
    && apk add --virtual virtual-deps \
          libmcrypt-dev \
          libxslt-dev \
          bzip2-dev \
          libpng-dev \
          gettext-dev \
          gmp-dev \
          icu-dev \
          libzip-dev \
          libwebp-dev \
          libjpeg-turbo-dev \
          freetype-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ --with-webp-dir=/usr/include/ \
    && docker-php-ext-configure zip --with-libzip=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        bz2 \
        exif \
        gd \
        gettext \
        gmp \
        intl \
        opcache \
        pcntl \
        pdo_mysql \
        soap \
        xsl \
        zip \
    && apk del virtual-deps

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm composer-setup.php

ADD resources /app/resources
ADD src /app/src
ADD tests /app/tests
ADD vendor /app/vendor
ADD cleaning_robot.php /app/
ADD composer.json /app/
ADD phpstan.neon /app/
ADD phpcs-enhanced.xml /app/
ADD README.md /app/

WORKDIR "/app"
ENTRYPOINT ["php", "cleaning_robot.php"]
