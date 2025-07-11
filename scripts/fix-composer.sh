#!/bin/bash

# Script para solucionar problemas de Composer en Hostinger
echo "🔧 Solucionando problemas de Composer..."

# Navegar al directorio del proyecto
cd /home/u835875601/domains/haxxsistem.host

# Eliminar vendor y composer.lock
echo "🗑️ Eliminando vendor y composer.lock..."
rm -rf vendor/
rm -f composer.lock

# Limpiar cache de Composer
echo "🧹 Limpiando cache de Composer..."
composer clear-cache

# Instalar dependencias de producción
echo "📦 Instalando dependencias..."
composer install --optimize-autoloader --no-dev --no-interaction

# Regenerar autoloader
echo "🔄 Regenerando autoloader..."
composer dump-autoload --optimize

# Limpiar cache de Laravel
echo "🧹 Limpiando cache de Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ ¡Problema solucionado!" 