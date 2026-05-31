#!/bin/bash
# Script de deploy para TuCancha — VPS DonWeb
# Ejecutar desde la raíz del proyecto

set -e

PHP=php84

# Timestamp para backups
TS=$(date +%Y%m%d_%H%M%S)

echo "==> [1/10] Activando modo mantenimiento..."
$PHP artisan down --retry=60 --secret="tucancha-deploy-$TS" || true

# ── Backup de DB antes de migrar ──────────────────────────
# Requiere que PGPASSWORD esté seteado en el entorno o ~/.pgpass
if command -v pg_dump >/dev/null 2>&1; then
  BACKUP_DIR="${HOME}/tucancha-backups"
  mkdir -p "$BACKUP_DIR"
  BACKUP_FILE="$BACKUP_DIR/tucancha_$TS.sql.gz"
  echo "==> [2/10] Creando backup de DB en $BACKUP_FILE..."
  # Toma DB_* del .env (no asume nada hardcodeado)
  DB_CONNECTION=$(grep -E "^DB_CONNECTION=" .env | cut -d= -f2 | tr -d '"')
  DB_HOST=$(grep -E "^DB_HOST=" .env | cut -d= -f2 | tr -d '"')
  DB_PORT=$(grep -E "^DB_PORT=" .env | cut -d= -f2 | tr -d '"')
  DB_DATABASE=$(grep -E "^DB_DATABASE=" .env | cut -d= -f2 | tr -d '"')
  DB_USERNAME=$(grep -E "^DB_USERNAME=" .env | cut -d= -f2 | tr -d '"')
  if [ "$DB_CONNECTION" = "pgsql" ]; then
    pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" -d "$DB_DATABASE" --no-owner --no-acl 2>/dev/null | gzip > "$BACKUP_FILE" || {
      echo "⚠️  Backup falló — abortando deploy por seguridad"
      $PHP artisan up
      exit 1
    }
    # Retener solo los últimos 10 backups
    ls -t "$BACKUP_DIR"/tucancha_*.sql.gz 2>/dev/null | tail -n +11 | xargs -r rm -f
    echo "    Backup OK. Tamaño: $(du -h $BACKUP_FILE | cut -f1)"
  fi
else
  echo "==> [2/10] ⚠️  pg_dump no disponible, saltando backup"
fi

echo "==> [3/10] Pulling latest code..."
git pull origin main

# ── Solo reinstalar dependencias JS si package-lock.json cambió ──
NEED_NPM_INSTALL=false
if git diff --name-only HEAD@{1} HEAD 2>/dev/null | grep -qE "package-lock\.json|package\.json"; then
  NEED_NPM_INSTALL=true
fi
if [ ! -d "public/build" ] || [ ! -f "public/build/manifest.json" ]; then
  NEED_NPM_INSTALL=true
fi

echo "==> [4/10] Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

if [ "$NEED_NPM_INSTALL" = true ]; then
  echo "==> [5/10] Installing JS dependencies and building assets..."
  npm ci --silent
  npm run build
  rm -rf node_modules
else
  echo "==> [5/10] npm: package-lock sin cambios, salteando reinstall"
fi

echo "==> [6/10] Running migrations..."
$PHP artisan migrate --force

echo "==> [7/10] Linking storage..."
$PHP artisan storage:link --force

# ── Symlinks de assets: el DocumentRoot (public_html) apunta fuera del
# public/ real de Laravel. Symlinkeamos las carpetas de assets para que
# Apache las sirva directo (si no, 404 en CSS/JS/imágenes). Idempotente.
PUBLIC_HTML="/home/santiago/public_html"
REAL_PUBLIC="/home/santiago/tucancha/public"
if [ -d "$PUBLIC_HTML" ]; then
  for asset in build css images storage site.webmanifest sw.js favicon.ico robots.txt; do
    if [ -e "$REAL_PUBLIC/$asset" ]; then
      ln -sfn "$REAL_PUBLIC/$asset" "$PUBLIC_HTML/$asset"
    fi
  done
  echo "    Symlinks de assets actualizados en $PUBLIC_HTML"
fi

echo "==> [8/10] Optimizando Laravel..."
$PHP artisan optimize:clear
$PHP artisan optimize
# optimize ya corre config:cache + route:cache + view:cache + event:cache

echo "==> [9/10] Reiniciando workers..."
$PHP artisan queue:restart
sudo supervisorctl restart tucancha-queue-worker:* 2>/dev/null || echo "  (queue-worker no encontrado, saltando)"
sudo supervisorctl restart tucancha-reverb:* 2>/dev/null || echo "  (reverb no encontrado, saltando)"

echo "==> [10/10] Desactivando modo mantenimiento..."
$PHP artisan up

echo ""
echo "✅ Deploy completado en $(date +%H:%M:%S)."
echo "   Backup: $BACKUP_FILE"
