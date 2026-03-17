# Despliegue en Railway

## Pasos

1. **Crear proyecto en Railway** y conectarlo al repositorio de `zippia-closet`.

2. **Añadir MySQL**  
   En Railway: **+ New** → **Database** → **Add MySQL**. Railway generará las variables de entorno de conexión.

3. **Variables de entorno obligatorias** (Settings → Variables):
   - `APP_KEY`: Generar con `php artisan key:generate --show`
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL`: URL de tu app en Railway (ej. `https://tu-proyecto.up.railway.app`)
   - `DB_CONNECTION=mysql`
   - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (se completan si añades el addon MySQL)
   - `JWT_SECRET`: Generar uno seguro (32+ caracteres aleatorios)

4. **Deploy**  
   Railway detecta el `Dockerfile` y desplegará el contenedor.

## Base de datos

Si usas el addon MySQL de Railway, las variables `DB_*` se rellenan solas. En el addon MySQL, revisa el panel y copia o referencia las variables que te proporcione Railway.

## Notas

- El contenedor usa **PHP 8.4** (requerido por tus dependencias).
- Al arrancar se ejecutan las migraciones.
- El servidor escucha en el puerto que asigne Railway (`$PORT`).
