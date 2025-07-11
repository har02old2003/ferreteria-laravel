#!/bin/bash

# Script específico para hosting compartido
echo "🏠 Configurando para hosting compartido..."

# Navegar al directorio del proyecto
cd /home/u835875601/domains/haxxsistem.host

# Configurar Composer para hosting compartido
composer config --global process-timeout 2000
composer config --global memory-limit 2G

# Instalar dependencias con configuración específica
echo "📦 Instalando dependencias para hosting compartido..."
composer install \
    --optimize-autoloader \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# Si falla, intentar con menos memoria
if [ $? -ne 0 ]; then
    echo "⚠️ Reintentando con configuración de memoria reducida..."
    COMPOSER_MEMORY_LIMIT=1G composer install \
        --optimize-autoloader \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-scripts
fi

# Regenerar autoloader
echo "🔄 Regenerando autoloader..."
composer dump-autoload --optimize --classmap-authoritative

# Configurar permisos
echo "🔐 Configurando permisos..."
chmod -R 755 .
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Limpiar y optimizar Laravel
echo "🧹 Limpiando y optimizando Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Configuración completada para hosting compartido" 