FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_mysql

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
