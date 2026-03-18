#!/bin/sh
set -e

cd /var/www/html

# Mapear variables de Railway MySQL a Laravel
export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-$MYSQLHOST}"
export DB_PORT="${DB_PORT:-$MYSQLPORT}"
export DB_DATABASE="${DB_DATABASE:-$MYSQLDATABASE}"
export DB_USERNAME="${DB_USERNAME:-$MYSQLUSER}"
export DB_PASSWORD="${DB_PASSWORD:-$MYSQLPASSWORD}"

# Generar APP_KEY si falta
if [ -z "$APP_KEY" ]; then
  export APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
fi

# Generar JWT_SECRET si falta
if [ -z "$JWT_SECRET" ]; then
  export JWT_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
fi

# Valores por defecto para producción
export APP_ENV="${APP_ENV:-production}"
export APP_DEBUG="${APP_DEBUG:-false}"
export APP_URL="${APP_URL:-}"

# Escribir TODAS las variables de entorno al .env (dinámico, no necesita lista fija)
php -r '
$file = "/var/www/html/.env";
$lines = [];
foreach (getenv() as $name => $val) {
  // Saltar variables internas del sistema/Docker que no son de Laravel
  if (in_array($name, ["PATH","HOME","HOSTNAME","TERM","SHLVL","PWD","_","DEBIAN_FRONTEND","COMPOSER_ALLOW_SUPERUSER","PHPIZE_DEPS","PHP_INI_DIR","PHP_CFLAGS","PHP_CPPFLAGS","PHP_LDFLAGS","PHP_VERSION","PHP_URL","PHP_ASC_URL","PHP_SHA256","GPG_KEYS","PHP_SHA256"], true)) {
    continue;
  }
  // Saltar variables de Railway internas (RAILWAY_*)
  if (str_starts_with($name, "RAILWAY_")) {
    continue;
  }
  $lines[] = $name . "=" . $val;
}
file_put_contents($file, implode("\n", $lines) . "\n");
'

php artisan config:clear
php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
