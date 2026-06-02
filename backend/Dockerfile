FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    openssl \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl \
    mbstring \
    opcache

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

RUN mkdir -p config/jwt \
    && if [ ! -f config/jwt/private.pem ]; then \
        openssl genpkey -algorithm RSA -out config/jwt/private.pem \
            -pkeyopt rsa_keygen_bits:4096 \
            -pass pass:${JWT_PASSPHRASE:-fitreserve_jwt_passphrase} 2>/dev/null; \
        openssl pkey -in config/jwt/private.pem \
            -passin pass:${JWT_PASSPHRASE:-fitreserve_jwt_passphrase} \
            -out config/jwt/public.pem -pubout 2>/dev/null; \
    fi

RUN chown -R www-data:www-data var/ || true

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
