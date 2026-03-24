#!/bin/bash
# Script de deploy para TuCancha — VPS DonWeb
# Ejecutar desde la raíz del proyecto

set -e

echo "==> Pulling latest code..."
git pull origin main

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing JS dependencies and building assets..."
npm ci
npm run build

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Clearing and caching config, routes, views y events..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Restarting queue workers..."
php artisan queue:restart

echo "==> Deploy completado."
