#!/bin/bash
# Script de deploy para TuCancha — VPS DonWeb
# Ejecutar desde la raíz del proyecto

set -e

PHP=php84

echo "==> Pulling latest code..."
git pull origin main

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing JS dependencies and building assets..."
npm ci
npm run build
rm -rf node_modules

echo "==> Running migrations..."
$PHP artisan migrate --force

echo "==> Linking storage..."
$PHP artisan storage:link --force

echo "==> Clearing and caching config, routes, views y events..."
$PHP artisan config:clear
$PHP artisan route:clear
$PHP artisan view:clear
$PHP artisan event:clear

$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

echo "==> Restarting queue workers..."
$PHP artisan queue:restart

echo "==> Restarting Supervisor worker..."
sudo supervisorctl restart tucancha-queue-worker:*

echo "==> Deploy completado."
