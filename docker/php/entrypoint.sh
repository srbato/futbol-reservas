#!/bin/bash
# Entrypoint del entorno de desarrollo.
#
# Todos los servicios PHP (app, queue, reverb, scheduler) usan la misma imagen.
# Sólo el servicio "app" hace el setup inicial (composer, key, migraciones);
# el resto únicamente espera a que la base y el vendor estén listos, para no
# correr migraciones cuatro veces en paralelo.
set -e

ROLE="${CONTAINER_ROLE:-app}"

wait_for_postgres() {
  echo "⏳ Esperando a PostgreSQL en ${DB_HOST:-db}:${DB_PORT:-5432}..."
  until pg_isready -h "${DB_HOST:-db}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-tucancha}" -q; do
    sleep 1
  done
  echo "✅ PostgreSQL listo."
}

wait_for_vendor() {
  # Los servicios secundarios arrancan a la par del "app"; esperan a que
  # termine de instalar dependencias antes de ejecutar artisan.
  local waited=0
  while [ ! -f vendor/autoload.php ] && [ $waited -lt 300 ]; do
    echo "⏳ Esperando a que se instalen las dependencias (composer)..."
    sleep 3
    waited=$((waited + 3))
  done
}

if [ "$ROLE" = "app" ]; then
  # ── .env ────────────────────────────────────────────────────────────
  if [ ! -f .env ]; then
    echo "📄 No hay .env — copiando desde .env.docker.example"
    cp .env.docker.example .env
  fi

  # ── Dependencias PHP ────────────────────────────────────────────────
  if [ ! -f vendor/autoload.php ]; then
    echo "📦 Instalando dependencias de Composer (puede tardar la primera vez)..."
    composer install --no-interaction --prefer-dist
  fi

  # ── APP_KEY ─────────────────────────────────────────────────────────
  if ! grep -qE '^APP_KEY=base64:.+' .env; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force
  fi

  wait_for_postgres

  # ── Migraciones ─────────────────────────────────────────────────────
  echo "🗄️  Corriendo migraciones..."
  php artisan migrate --force

  # ── Storage link ────────────────────────────────────────────────────
  if [ ! -e public/storage ]; then
    echo "🔗 Creando symlink de storage..."
    php artisan storage:link
  fi

  echo ""
  echo "✅ TuCancha listo → http://localhost:8000"
  echo "   Si es la primera vez, cargá datos de prueba con:"
  echo "   docker compose exec app php artisan migrate:fresh --seed"
  echo ""
else
  wait_for_vendor
  wait_for_postgres
fi

exec "$@"
