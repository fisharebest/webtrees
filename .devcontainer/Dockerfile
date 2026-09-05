FROM php:8.5-cli-bookworm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=node:22-bookworm /usr/local/ /usr/local/

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libsqlite3-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j1 gd \
    && docker-php-ext-install -j1 intl \
    && docker-php-ext-install -j1 mbstring \
    && docker-php-ext-install -j1 exif \
    && docker-php-ext-install -j1 pdo_sqlite \
    && docker-php-ext-install -j1 zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /workspaces/webtrees