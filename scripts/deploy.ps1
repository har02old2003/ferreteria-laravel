# Script de Despliegue para Hostinger (PowerShell)
# Uso: .\scripts\deploy.ps1

param(
    [switch]$SkipBuild,
    [switch]$SkipOptimize
)

# Configurar para detener en errores
$ErrorActionPreference = "Stop"

Write-Host "🚀 Iniciando proceso de despliegue para Hostinger..." -ForegroundColor Green

# Función para imprimir mensajes
function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Verificar que estamos en el directorio raíz del proyecto
if (-not (Test-Path "artisan")) {
    Write-Error "Este script debe ejecutarse desde el directorio raíz del proyecto Laravel"
    exit 1
}

Write-Info "Verificando requisitos..."

# Verificar si Composer está instalado
try {
    $composerVersion = composer --version 2>$null
    if (-not $composerVersion) {
        throw "Composer no encontrado"
    }
    Write-Info "Composer encontrado: $composerVersion"
} catch {
    Write-Error "Composer no está instalado. Por favor instálalo primero."
    exit 1
}

# Verificar si Node.js está instalado
try {
    $nodeVersion = node --version 2>$null
    if (-not $nodeVersion) {
        throw "Node.js no encontrado"
    }
    Write-Info "Node.js encontrado: $nodeVersion"
} catch {
    Write-Error "Node.js no está instalado. Por favor instálalo primero."
    exit 1
}

# Verificar si npm está instalado
try {
    $npmVersion = npm --version 2>$null
    if (-not $npmVersion) {
        throw "npm no encontrado"
    }
    Write-Info "npm encontrado: $npmVersion"
} catch {
    Write-Error "npm no está instalado. Por favor instálalo primero."
    exit 1
}

if (-not $SkipBuild) {
    Write-Info "Instalando dependencias de producción..."
    
    # Instalar dependencias de Composer para producción
    composer install --optimize-autoloader --no-dev --no-interaction
    
    Write-Info "Instalando dependencias de Node.js..."
    
    # Instalar dependencias de npm
    npm install
    
    Write-Info "Compilando assets para producción..."
    
    # Compilar assets para producción
    npm run build
}

Write-Info "Generando clave de aplicación..."

# Generar clave de aplicación si no existe
if (-not (Test-Path ".env")) {
    Write-Warning "Archivo .env no encontrado. Copiando .env.example..."
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
    } else {
        Write-Error "Archivo .env.example no encontrado"
        exit 1
    }
}

# Generar clave de aplicación
php artisan key:generate --force

if (-not $SkipOptimize) {
    Write-Info "Limpiando y cacheando configuraciones..."
    
    # Limpiar cache
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Cachear configuraciones para producción
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    Write-Info "Optimizando autoloader..."
    
    # Optimizar autoloader
    composer dump-autoload --optimize
}

Write-Info "Verificando permisos de directorios..."

# Crear directorios necesarios si no existen
$directories = @(
    "storage/framework/cache",
    "storage/framework/sessions", 
    "storage/framework/views",
    "bootstrap/cache"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Info "Directorio creado: $dir"
    }
}

Write-Info "Creando archivo de configuración para Hostinger..."

# Crear archivo de configuración específico para Hostinger
$envContent = @"
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
MAIL_FROM_NAME="`${APP_NAME}"
"@

$envContent | Out-File -FilePath ".env.hostinger" -Encoding UTF8

Write-Info "Creando archivo .htaccess optimizado..."

# Crear .htaccess optimizado para Hostinger
$htaccessContent = @"
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
"@

$htaccessContent | Out-File -FilePath "public/.htaccess" -Encoding UTF8

Write-Info "Creando lista de archivos para subir..."

# Crear archivo con lista de archivos a excluir
$excludeContent = @"
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
"@

$excludeContent | Out-File -FilePath ".deploy-exclude" -Encoding UTF8

Write-Info "✅ Proceso de preparación completado exitosamente!"

Write-Host ""
Write-Host "📋 Próximos pasos:" -ForegroundColor Cyan
Write-Host "1. Edita el archivo .env.hostinger con tus credenciales de Hostinger"
Write-Host "2. Sube los archivos a tu servidor (excluyendo los archivos en .deploy-exclude)"
Write-Host "3. Mueve el contenido de la carpeta 'public' al directorio raíz del sitio web"
Write-Host "4. Mueve todos los demás archivos y carpetas a un nivel superior"
Write-Host "5. Configura la base de datos en Hostinger"
Write-Host "6. Ejecuta las migraciones: php artisan migrate --force"
Write-Host "7. Configura los permisos correctos en el servidor"
Write-Host ""
Write-Host "📖 Consulta el archivo DEPLOYMENT.md para instrucciones detalladas"
Write-Host ""

Write-Info "¡Tu proyecto está listo para ser desplegado en Hostinger!" 