#!/bin/sh
set -e

# Symfony a besoin du fichier .env pour booter même si les vars sont dans l'env
touch /var/www/html/.env

if [ ! -f /var/www/html/vendor/autoload_runtime.php ]; then
    echo "[FitReserve] Installation des dépendances Composer..."
    composer install --no-interaction --optimize-autoloader
fi

if [ ! -f /var/www/html/config/jwt/private.pem ]; then
    echo "[FitReserve] Génération des clés JWT..."
    openssl genpkey -algorithm RSA \
        -out /var/www/html/config/jwt/private.pem \
        -pkeyopt rsa_keygen_bits:4096 \
        -pass pass:"${JWT_PASSPHRASE:-fitreserve_jwt_passphrase}"
    openssl pkey \
        -in /var/www/html/config/jwt/private.pem \
        -passin pass:"${JWT_PASSPHRASE:-fitreserve_jwt_passphrase}" \
        -out /var/www/html/config/jwt/public.pem -pubout
fi

echo "[FitReserve] Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "[FitReserve] Cache..."
php bin/console cache:warmup --env=prod 2>/dev/null || true

exec "$@"
