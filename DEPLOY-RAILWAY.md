# Despliegue en Railway

## Pasos

1. **Crear proyecto en Railway** y conectarlo al repositorio de `zippia-closet`.

2. **Añadir MySQL**  
   En Railway: **+ New** → **Database** → **Add MySQL**. Luego, en tu servicio Laravel: **Variables** → **Add Variable** → **Add Reference** y selecciona las variables del servicio MySQL. El entrypoint mapea automáticamente `MYSQLHOST`→`DB_HOST`, `MYSQLPORT`→`DB_PORT`, etc.

   Si no usa referencias, añade manualmente:
   - `DB_HOST` = Referencia a `MYSQLHOST` del addon MySQL
   - `DB_PORT` = Referencia a `MYSQLPORT`
   - `DB_DATABASE` = Referencia a `MYSQLDATABASE`
   - `DB_USERNAME` = Referencia a `MYSQLUSER`
   - `DB_PASSWORD` = Referencia a `MYSQLPASSWORD`

3. **Variables de entorno obligatorias** (en el servicio Laravel):
   - `APP_KEY`: Generar con `php artisan key:generate --show` (o se genera solo si falta)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL`: URL de tu app (ej. `https://tu-proyecto.up.railway.app`)
   - `JWT_SECRET`: Generar uno seguro (32+ caracteres)

4. **Deploy**  
   Railway detecta el `Dockerfile` y desplegará el contenedor.

## Base de datos

Si usas el addon MySQL de Railway, las variables `DB_*` se rellenan solas. En el addon MySQL, revisa el panel y copia o referencia las variables que te proporcione Railway.

## Notas

- El contenedor usa **PHP 8.4** (requerido por tus dependencias).
- Al arrancar se ejecutan las migraciones.
- El servidor escucha en el puerto que asigne Railway (`$PORT`).
