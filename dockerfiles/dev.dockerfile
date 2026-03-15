# medwinv1api/dockerfiles/dev.dockerfile
# Dependencies (vendor) are installed via the composer utility service and bind-mounted at runtime.
FROM php:8.1-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libgmp-dev \
    libicu-dev \
    libpq-dev \
    libsqlite3-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       bcmath \
       gmp \
       mbstring \
       zip \
       pdo \
       pdo_mysql \
       pdo_pgsql \
       gd \
       intl \
       soap \
       opcache \
       pcntl \
       curl \
       xml \
       sockets \
       mysqli \
       exif

# Install Composer (for artisan and ad-hoc composer commands)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Xdebug (dev)
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug config: connect to IDE on host
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Allow uploads up to 100MB and long-running requests (match nginx timeouts)
RUN echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www
