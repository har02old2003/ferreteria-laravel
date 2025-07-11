# Script para crear archivo .env.hostinger
# Uso: .\scripts\create-env.ps1

Write-Host "🔧 Creando archivo .env.hostinger..." -ForegroundColor Green

$envContent = @"
APP_NAME="Sistema de Gestión Ferretería"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dbfarma
DB_USERNAME=
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

VITE_APP_NAME="`${APP_NAME}"
VITE_PUSHER_APP_KEY="`${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="`${PUSHER_HOST}"
VITE_PUSHER_PORT="`${PUSHER_PORT}"
VITE_PUSHER_SCHEME="`${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="`${PUSHER_APP_CLUSTER}"
"@

# Crear el archivo .env.hostinger
$envContent | Out-File -FilePath ".env.hostinger" -Encoding UTF8

Write-Host "✅ Archivo .env.hostinger creado exitosamente!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Ahora edita el archivo .env.hostinger con tus datos reales:" -ForegroundColor Yellow
Write-Host "   - APP_URL: tu dominio real"
Write-Host "   - DB_DATABASE: nombre de tu base de datos"
Write-Host "   - DB_USERNAME: usuario de tu base de datos"
Write-Host "   - DB_PASSWORD: contraseña de tu base de datos"
Write-Host ""
Write-Host "💡 Ejemplo:" -ForegroundColor Cyan
Write-Host "   APP_URL=https://miferreteria.com"
Write-Host "   DB_DATABASE=ferreteria_db"
Write-Host "   DB_USERNAME=ferreteria_user"
Write-Host "   DB_PASSWORD=mi_contraseña_segura" 