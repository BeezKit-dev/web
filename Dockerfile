FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker-entrypoint.sh /usr/local/bin/beezkit-entrypoint
RUN chmod +x /usr/local/bin/beezkit-entrypoint

COPY . /app

ENTRYPOINT ["beezkit-entrypoint"]

CMD ["php", "artisan", "octane:frankenphp"]
