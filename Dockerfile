FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
