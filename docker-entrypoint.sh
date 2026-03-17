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

# Escribir todas las variables necesarias en .env
php -r '
$file = "/var/www/html/.env";
$content = file_exists($file) ? file_get_contents($file) : "";
$vars = [
  "APP_KEY", "APP_ENV", "APP_DEBUG", "APP_URL",
  "DB_CONNECTION", "DB_HOST", "DB_PORT", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD",
  "JWT_SECRET"
];
foreach ($vars as $name) {
  $val = getenv($name);
  if ($val !== false && $val !== "") {
    $pattern = "/^" . preg_quote($name, "/") . "=.*/m";
    if (preg_match($pattern, $content)) {
      $content = preg_replace($pattern, $name . "=" . $val, $content);
    } else {
      $content = trim($content) . "\n" . $name . "=" . $val . "\n";
    }
  }
}
file_put_contents($file, $content);
'

php artisan config:clear
php artisan migrate --force
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
