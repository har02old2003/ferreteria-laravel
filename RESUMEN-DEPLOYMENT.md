# Resumen Ejecutivo - Despliegue en Hostinger

## 🚀 Pasos Rápidos (5 minutos)

### 1. Preparar el Proyecto
```powershell
# Ejecutar desde el directorio raíz del proyecto
.\scripts\deploy.ps1
```

### 2. Configurar Credenciales
- Editar archivo `.env.hostinger` con tus datos de Hostinger
- Cambiar `tudominio.com` por tu dominio real
- Cambiar credenciales de base de datos

### 3. Subir Archivos
**Para Hosting Compartido:**
1. Acceder a cPanel → File Manager
2. Ir a `public_html`
3. Subir todos los archivos (excepto los de `.deploy-exclude`)
4. **MOVER** contenido de carpeta `public` al directorio raíz
5. **MOVER** todos los demás archivos un nivel arriba

### 4. Configurar Base de Datos
```bash
# En Terminal de cPanel o SSH
php artisan migrate --force
```

### 5. Configurar Permisos
```bash
chmod -R 755 /home/usuario/public_html
chmod -R 775 storage bootstrap/cache
```

## 📁 Estructura Final en Hostinger

```
public_html/
├── index.php          ← desde public/
├── .htaccess          ← desde public/
├── build/             ← assets compilados
├── app/               ← aplicación Laravel
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── vendor/
```

## ⚙️ Configuraciones Importantes

### Archivo .env
```env
APP_NAME="Tu Sistema"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_password_db
```

### Base de Datos en Hostinger
1. cPanel → Bases de datos MySQL
2. Crear base de datos
3. Crear usuario
4. Asignar permisos completos

## 🔧 Comandos Útiles

### Verificar Estado
```bash
php artisan about
php artisan route:list
```

### Limpiar Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimizar para Producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🚨 Problemas Comunes

### Error 500
- Verificar permisos: `chmod -R 755 public_html`
- Revisar logs: `tail -f storage/logs/laravel.log`
- Verificar `.env` existe y está configurado

### Assets no Cargan
- Verificar que `npm run build` se ejecutó
- Verificar archivo `public/build/manifest.json`
- Limpiar cache del navegador

### Error de Base de Datos
- Verificar credenciales en `.env`
- Confirmar base de datos existe
- Verificar permisos del usuario

## 📞 Soporte

- **Logs de error**: `storage/logs/laravel.log`
- **Documentación completa**: `DEPLOYMENT.md`
- **Checklist detallado**: `CHECKLIST-DEPLOYMENT.md`
- **Soporte Hostinger**: Panel de control → Soporte

## ⏱️ Tiempo Estimado

- **Preparación**: 5 minutos
- **Subida de archivos**: 10-30 minutos (depende del tamaño)
- **Configuración**: 10 minutos
- **Pruebas**: 15 minutos
- **Total**: ~1 hora

---

**¡Tu sistema Laravel estará funcionando en Hostinger en menos de 1 hora!** 