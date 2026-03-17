#!/bin/sh
set -e

cd /var/www/html

# Asegurar APP_KEY en .env (Laravel la lee al cargar el archivo)
if [ -z "$APP_KEY" ]; then
  APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
  export APP_KEY="$APP_KEY"
fi

# Escribir APP_KEY en .env para que Laravel la encuentre
php -r "
\$file = '/var/www/html/.env';
\$content = file_exists(\$file) ? file_get_contents(\$file) : '';
\$key = getenv('APP_KEY');
if (preg_match('/^APP_KEY=/m', \$content)) {
  \$content = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . \$key, \$content);
} else {
  \$content = trim(\$content) . \"\nAPP_KEY=\$key\n\";
}
file_put_contents(\$file, \$content);
"

php artisan config:clear
php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
