#!/bin/sh
set -e

cd /var/www/html

# Mapear variables de Railway MySQL a Laravel si no están definidas
export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-$MYSQLHOST}"
export DB_PORT="${DB_PORT:-$MYSQLPORT}"
export DB_DATABASE="${DB_DATABASE:-$MYSQLDATABASE}"
export DB_USERNAME="${DB_USERNAME:-$MYSQLUSER}"
export DB_PASSWORD="${DB_PASSWORD:-$MYSQLPASSWORD}"

# Asegurar APP_KEY en .env
if [ -z "$APP_KEY" ]; then
  APP_KEY=$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")
  export APP_KEY="$APP_KEY"
fi

# Escribir variables en .env para que Laravel las cargue
php -r '
$file = "/var/www/html/.env";
$content = file_exists($file) ? file_get_contents($file) : "";
$vars = ["APP_KEY", "DB_CONNECTION", "DB_HOST", "DB_PORT", "DB_DATABASE", "DB_USERNAME", "DB_PASSWORD"];
foreach ($vars as $name) {
  $val = getenv($name);
  if ($val !== false) {
    $pattern = "/^" . $name . "=.*/m";
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
