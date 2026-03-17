#!/bin/sh
set -e

# Generar APP_KEY si no está definida o está vacía
if [ -z "$APP_KEY" ]; then
  APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
  echo "APP_KEY=$APP_KEY" >> /var/www/html/.env
  export APP_KEY="$APP_KEY"
fi

php artisan config:clear
php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
