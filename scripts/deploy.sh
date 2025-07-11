#!/bin/bash

# Script de Despliegue para Hostinger
# Uso: ./scripts/deploy.sh

set -e

echo "🚀 Iniciando proceso de despliegue para Hostinger..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_message() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar que estamos en el directorio raíz del proyecto
if [ ! -f "artisan" ]; then
    print_error "Este script debe ejecutarse desde el directorio raíz del proyecto Laravel"
    exit 1
fi

print_message "Verificando requisitos..."

# Verificar si Composer está instalado
if ! command -v composer &> /dev/null; then
    print_error "Composer no está instalado. Por favor instálalo primero."
    exit 1
fi

# Verificar si Node.js está instalado
if ! command -v node &> /dev/null; then
    print_error "Node.js no está instalado. Por favor instálalo primero."
    exit 1
fi

# Verificar si npm está instalado
if ! command -v npm &> /dev/null; then
    print_error "npm no está instalado. Por favor instálalo primero."
    exit 1
fi

print_message "Instalando dependencias de producción..."

# Instalar dependencias de Composer para producción
composer install --optimize-autoloader --no-dev --no-interaction

print_message "Instalando dependencias de Node.js..."

# Instalar dependencias de npm
npm install

print_message "Compilando assets para producción..."

# Compilar assets para producción
npm run build

print_message "Generando clave de aplicación..."

# Generar clave de aplicación si no existe
if [ ! -f ".env" ]; then
    print_warning "Archivo .env no encontrado. Copiando .env.example..."
    cp .env.example .env
fi

# Generar clave de aplicación
php artisan key:generate --force

print_message "Limpiando y cacheando configuraciones..."

# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cachear configuraciones para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_message "Optimizando autoloader..."

# Optimizar autoloader
composer dump-autoload --optimize

print_message "Verificando permisos de directorios..."

# Crear directorios necesarios si no existen
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Establecer permisos correctos
chmod -R 755 storage
chmod -R 755 bootstrap/cache

print_message "Creando archivo de configuración para Hostinger..."

# Crear archivo de configuración específico para Hostinger
cat > .env.hostinger << 'EOF'
APP_NAME="Sistema de Gestión"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=tu_servidor_smtp
MAIL_PORT=587
MAIL_USERNAME=tu_email
MAIL_PASSWORD=tu_password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tudominio.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF

print_message "Creando archivo .htaccess optimizado..."

# Crear .htaccess optimizado para Hostinger
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    # Security Headers
    <IfModule mod_headers.c>
        Header always set X-Content-Type-Options nosniff
        Header always set X-Frame-Options DENY
        Header always set X-XSS-Protection "1; mode=block"
        Header always set Referrer-Policy "strict-origin-when-cross-origin"
    </IfModule>

    # Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/plain
        AddOutputFilterByType DEFLATE text/html
        AddOutputFilterByType DEFLATE text/xml
        AddOutputFilterByType DEFLATE text/css
        AddOutputFilterByType DEFLATE application/xml
        AddOutputFilterByType DEFLATE application/xhtml+xml
        AddOutputFilterByType DEFLATE application/rss+xml
        AddOutputFilterByType DEFLATE application/javascript
        AddOutputFilterByType DEFLATE application/x-javascript
    </IfModule>

    # Cache Control
    <IfModule mod_expires.c>
        ExpiresActive on
        ExpiresByType text/css "access plus 1 year"
        ExpiresByType application/javascript "access plus 1 year"
        ExpiresByType image/png "access plus 1 year"
        ExpiresByType image/jpg "access plus 1 year"
        ExpiresByType image/jpeg "access plus 1 year"
        ExpiresByType image/gif "access plus 1 year"
        ExpiresByType image/ico "access plus 1 year"
        ExpiresByType image/icon "access plus 1 year"
        ExpiresByType text/plain "access plus 1 month"
        ExpiresByType application/pdf "access plus 1 month"
        ExpiresByType application/x-shockwave-flash "access plus 1 month"
    </IfModule>
</IfModule>
EOF

print_message "Creando lista de archivos para subir..."

# Crear archivo con lista de archivos a excluir
cat > .deploy-exclude << 'EOF'
node_modules/
vendor/
.git/
.gitignore
.env
.env.local
.env.example
.env.hostinger
.htaccess
README.md
DEPLOYMENT.md
composer.lock
package-lock.json
yarn.lock
*.log
.DS_Store
Thumbs.db
.vscode/
.idea/
*.swp
*.swo
*~
EOF

print_message "✅ Proceso de preparación completado exitosamente!"

echo ""
echo "📋 Próximos pasos:"
echo "1. Edita el archivo .env.hostinger con tus credenciales de Hostinger"
echo "2. Sube los archivos a tu servidor (excluyendo los archivos en .deploy-exclude)"
echo "3. Mueve el contenido de la carpeta 'public' al directorio raíz del sitio web"
echo "4. Mueve todos los demás archivos y carpetas a un nivel superior"
echo "5. Configura la base de datos en Hostinger"
echo "6. Ejecuta las migraciones: php artisan migrate --force"
echo "7. Configura los permisos correctos en el servidor"
echo ""
echo "📖 Consulta el archivo DEPLOYMENT.md para instrucciones detalladas"
echo ""

print_message "¡Tu proyecto está listo para ser desplegado en Hostinger!" 