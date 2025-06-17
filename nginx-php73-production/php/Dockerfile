FROM php:7.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client \
    && rm -rf /var/cache/apk/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        zip \
        gd \
        opcache \
        bcmath \
        intl

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/php.ini

# Copy PHP-FPM configuration
COPY php-fpm.d/ /usr/local/etc/php-fpm.d/

# Create log directory
RUN mkdir -p /var/log/php-fpm \
    && chown -R www-data:www-data /var/log/php-fpm

# Set working directory
WORKDIR /var/www/html

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"] 