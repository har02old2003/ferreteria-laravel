#!/bin/bash

# Script para verificar configuración de PHP
echo "🔍 Verificando configuración de PHP..."

# Verificar versión de PHP
echo "📋 Versión de PHP:"
php -v

# Verificar extensiones requeridas
echo ""
echo "📋 Extensiones PHP instaladas:"
php -m | grep -E "(mbstring|openssl|pdo|tokenizer|xml|ctype|json|bcmath|fileinfo)"

# Verificar límites de memoria
echo ""
echo "📋 Límites de memoria:"
php -r "echo 'Memory limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo 'Max execution time: ' . ini_get('max_execution_time') . PHP_EOL;"
php -r "echo 'Upload max filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"

# Verificar permisos de directorios
echo ""
echo "📋 Permisos de directorios:"
ls -la storage/
ls -la bootstrap/cache/

echo "✅ Verificación completada" 