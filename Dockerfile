FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev libzip-dev libicu-dev unzip git \
    && docker-php-ext-install pdo_pgsql zip intl bcmath opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} app 2>/dev/null; useradd -u ${UID} -g ${GID} -m app 2>/dev/null; true
USER ${UID}:${GID}

WORKDIR /var/www/html
