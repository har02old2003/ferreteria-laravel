# 🚀 Guía de Instalación del Proyecto

## Requisitos Previos

### Software requerido:
- ✅ **PHP 8.2+** ([Descargar XAMPP](https://www.apachefriends.org/))
- ✅ **Composer** ([Descargar](https://getcomposer.org/download/))
- ✅ **Node.js 18+** ([Descargar](https://nodejs.org/))
- ✅ **MySQL 8.0+** (incluido en XAMPP)
- ✅ **Git** ([Descargar](https://git-scm.com/))

### Editores recomendados:
- 🎯 **VS Code** con extensiones: PHP Intelephense, Laravel Blade, Vue Language Features
- 🎯 **PHPStorm** (opción premium)

## 📥 Instalación

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd [NOMBRE_DEL_PROYECTO]
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar entorno

#### 4.1. Crear archivo .env
```bash
# Si existe .env.example:
cp .env.example .env

# Si NO existe, crear manualmente el archivo .env con este contenido:
```

#### 4.2. Contenido del archivo .env
Crea el archivo `.env` en la raíz del proyecto con este contenido:
```env
APP_NAME="Sistema Ferretería"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Guatemala
APP_URL=http://localhost:8000

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES

LOG_CHANNEL=stack
LOG_LEVEL=debug

# CONFIGURAR ESTOS VALORES CON TUS CREDENCIALES
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_bd
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

SESSION_DRIVER=database
SESSION_LIFETIME=120

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@ferreteria.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"

# Fortify Features
FORTIFY_FEATURES=registration,reset-passwords,email-verification,update-profile-information,update-passwords,two-factor-authentication

# Sistema
SYSTEM_CURRENCY=GTQ
SYSTEM_TIMEZONE=America/Guatemala
METRICS_ENABLED=true
```

#### 4.3. Generar clave de aplicación
```bash
php artisan key:generate
```

### 5. Ejecutar migraciones
```bash
php artisan migrate
```

### 6. Poblar base de datos (opcional)
```bash
php artisan db:seed
```

### 7. Compilar assets
```bash
npm run build
# O para desarrollo:
npm run dev
```

## 🚀 Ejecutar el proyecto

### Opción 1: Comando rápido (recomendado)
```bash
composer run dev
```
Esto ejecuta automáticamente: servidor PHP + queue worker + Vite dev server

### Opción 2: Manual (en terminales separadas)
```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Assets en modo desarrollo
npm run dev

# Terminal 3: Queue worker (si usas jobs)
php artisan queue:work
```

## 🔗 URLs de acceso
- **Frontend**: http://localhost:8000
- **Base de datos**: http://localhost/phpmyadmin (si usas XAMPP)

## 🧪 Testing
```bash
# Ejecutar tests
php artisan test

# Con cobertura
composer run test-coverage
```

## 🆘 Problemas comunes

### Error de permisos (Linux/Mac)
```bash
sudo chmod -R 755 storage bootstrap/cache
```

### Error de Composer
```bash
composer clear-cache
composer install --no-cache
```

### Error de Node.js
```bash
rm -rf node_modules package-lock.json
npm install
```

## 📞 Soporte
Si tienes problemas, contacta al equipo de desarrollo o crea un issue en GitHub. 