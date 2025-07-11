# Guía de Despliegue en Hostinger

## Requisitos Previos

- Cuenta en Hostinger con hosting compartido o VPS
- Acceso a cPanel (para hosting compartido) o SSH (para VPS)
- Base de datos MySQL creada en Hostinger
- Dominio configurado

## Paso 1: Preparar el Proyecto Localmente

### 1.1 Optimizar para Producción

```bash
# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Compilar assets
npm install
npm run build

# Generar clave de aplicación
php artisan key:generate

# Limpiar y cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 1.2 Crear archivo .env para producción

Crea un archivo `.env` con las siguientes configuraciones:

```env
APP_NAME="Tu Sistema"
APP_ENV=production
APP_KEY=base64:tu_clave_generada
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
```

## Paso 2: Subir Archivos a Hostinger

### 2.1 Para Hosting Compartido (cPanel)

1. **Acceder a cPanel** y abrir el **File Manager**
2. **Navegar** a la carpeta `public_html` o `www`
3. **Subir** todos los archivos del proyecto (excepto `node_modules`, `vendor`, `.git`)
4. **Mover** el contenido de la carpeta `public` al directorio raíz del sitio web
5. **Mover** todos los demás archivos y carpetas a un nivel superior

### 2.2 Para VPS (SSH)

```bash
# Conectar por SSH
ssh usuario@tu-servidor.com

# Navegar al directorio del sitio web
cd /home/usuario/public_html

# Subir archivos (desde tu máquina local)
scp -r ./proyecto/* usuario@tu-servidor.com:/home/usuario/public_html/

# O usar rsync
rsync -avz --exclude='node_modules' --exclude='vendor' --exclude='.git' ./proyecto/ usuario@tu-servidor.com:/home/usuario/public_html/
```

## Paso 3: Configurar la Base de Datos

### 3.1 Crear Base de Datos en Hostinger

1. **Acceder a cPanel** → **Bases de datos MySQL**
2. **Crear nueva base de datos**
3. **Crear usuario de base de datos**
4. **Asignar permisos** al usuario en la base de datos

### 3.2 Ejecutar Migraciones

```bash
# Conectar por SSH o usar Terminal de cPanel
php artisan migrate --force

# Si tienes seeders
php artisan db:seed --force
```

## Paso 4: Configurar Permisos

```bash
# Establecer permisos correctos
chmod -R 755 /home/usuario/public_html
chmod -R 775 /home/usuario/public_html/storage
chmod -R 775 /home/usuario/public_html/bootstrap/cache

# Cambiar propietario (si es necesario)
chown -R usuario:usuario /home/usuario/public_html
```

## Paso 5: Configurar el Servidor Web

### 5.1 Para Apache (Hosting Compartido)

El archivo `.htaccess` ya está configurado en la carpeta `public`.

### 5.2 Para Nginx (VPS)

Crear archivo de configuración en `/etc/nginx/sites-available/tu-dominio`:

```nginx
server {
    listen 80;
    server_name tudominio.com www.tudominio.com;
    root /home/usuario/public_html/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Paso 6: Verificar la Instalación

1. **Visitar** tu dominio en el navegador
2. **Verificar** que la aplicación carga correctamente
3. **Revisar** los logs de error si hay problemas:
   ```bash
   tail -f /home/usuario/public_html/storage/logs/laravel.log
   ```

## Paso 7: Configuraciones Adicionales

### 7.1 Configurar SSL (HTTPS)

1. **Activar SSL** desde cPanel → **SSL/TLS**
2. **Configurar redirección** de HTTP a HTTPS en `.htaccess`

### 7.2 Configurar Email

Actualizar configuraciones de email en `.env` con los datos de Hostinger.

### 7.3 Configurar Backup

Configurar backups automáticos de la base de datos desde cPanel.

## Solución de Problemas Comunes

### Error 500
- Verificar permisos de archivos
- Revisar logs de error
- Verificar configuración de `.env`

### Error de Base de Datos
- Verificar credenciales en `.env`
- Confirmar que la base de datos existe
- Verificar permisos del usuario de BD

### Assets no Cargan
- Verificar que `npm run build` se ejecutó
- Verificar rutas en `vite.config.js`
- Limpiar cache del navegador

### Problemas de Permisos
```bash
chmod -R 755 /home/usuario/public_html
chmod -R 775 storage bootstrap/cache
```

## Comandos Útiles para Mantenimiento

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar estado
php artisan about
php artisan route:list
```

## Contacto y Soporte

Si tienes problemas durante el despliegue:
1. Revisar logs de error
2. Verificar configuraciones
3. Contactar soporte de Hostinger
4. Consultar documentación de Laravel 