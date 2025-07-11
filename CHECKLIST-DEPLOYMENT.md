# Checklist de Despliegue en Hostinger

## ✅ Preparación Local

### Antes de comenzar:
- [ ] Tener cuenta en Hostinger activa
- [ ] Tener acceso a cPanel o SSH
- [ ] Tener dominio configurado
- [ ] Tener base de datos MySQL creada en Hostinger

### Preparar el proyecto:
- [ ] Ejecutar script de preparación: `.\scripts\deploy.ps1`
- [ ] Verificar que se generó el archivo `.env.hostinger`
- [ ] Verificar que se generó el archivo `.deploy-exclude`
- [ ] Verificar que se actualizó el archivo `public/.htaccess`

### Configurar credenciales:
- [ ] Editar `.env.hostinger` con datos reales:
  - [ ] `APP_URL` con tu dominio
  - [ ] `DB_DATABASE` con nombre de tu base de datos
  - [ ] `DB_USERNAME` con usuario de la base de datos
  - [ ] `DB_PASSWORD` con contraseña de la base de datos
  - [ ] Configuraciones de email si las necesitas

## ✅ Subir Archivos

### Para Hosting Compartido (cPanel):
- [ ] Acceder a cPanel → File Manager
- [ ] Navegar a `public_html` o `www`
- [ ] Subir todos los archivos (excepto los listados en `.deploy-exclude`)
- [ ] **IMPORTANTE**: Mover contenido de carpeta `public` al directorio raíz
- [ ] Mover todos los demás archivos y carpetas a un nivel superior

### Para VPS (SSH):
- [ ] Conectar por SSH al servidor
- [ ] Navegar al directorio del sitio web
- [ ] Subir archivos usando SCP o rsync
- [ ] Mover archivos según la estructura requerida

## ✅ Configurar Base de Datos

### En Hostinger:
- [ ] Acceder a cPanel → Bases de datos MySQL
- [ ] Verificar que la base de datos existe
- [ ] Verificar que el usuario tiene permisos completos
- [ ] Probar conexión a la base de datos

### Ejecutar migraciones:
- [ ] Conectar por SSH o usar Terminal de cPanel
- [ ] Ejecutar: `php artisan migrate --force`
- [ ] Si tienes seeders: `php artisan db:seed --force`
- [ ] Verificar que las tablas se crearon correctamente

## ✅ Configurar Permisos

### Establecer permisos correctos:
- [ ] `chmod -R 755` en el directorio raíz
- [ ] `chmod -R 775` en `storage`
- [ ] `chmod -R 775` en `bootstrap/cache`
- [ ] Verificar que el usuario web puede escribir en estos directorios

## ✅ Configurar Archivos

### Renombrar archivo de configuración:
- [ ] Renombrar `.env.hostinger` a `.env`
- [ ] Verificar que todas las variables están configuradas

### Verificar estructura de archivos:
```
public_html/
├── index.php (desde public/)
├── .htaccess (desde public/)
├── build/ (assets compilados)
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── vendor/
```

## ✅ Verificar Funcionamiento

### Pruebas básicas:
- [ ] Visitar el dominio en el navegador
- [ ] Verificar que la página principal carga
- [ ] Verificar que los assets (CSS/JS) cargan correctamente
- [ ] Probar el login del sistema
- [ ] Verificar que las funcionalidades principales funcionan

### Verificar logs:
- [ ] Revisar `storage/logs/laravel.log` para errores
- [ ] Verificar logs del servidor web si hay problemas

## ✅ Configuraciones Adicionales

### SSL/HTTPS:
- [ ] Activar SSL desde cPanel → SSL/TLS
- [ ] Configurar redirección HTTP a HTTPS
- [ ] Actualizar `APP_URL` en `.env` para usar HTTPS

### Email:
- [ ] Configurar SMTP en `.env` con datos de Hostinger
- [ ] Probar envío de emails
- [ ] Configurar notificaciones del sistema

### Backup:
- [ ] Configurar backup automático de la base de datos
- [ ] Configurar backup de archivos importantes
- [ ] Documentar proceso de restauración

## ✅ Optimizaciones

### Performance:
- [ ] Verificar que el cache está funcionando
- [ ] Optimizar imágenes si es necesario
- [ ] Configurar CDN si está disponible
- [ ] Verificar tiempos de carga

### Seguridad:
- [ ] Verificar que `.env` no es accesible públicamente
- [ ] Verificar que `storage` no es accesible públicamente
- [ ] Configurar firewall si es necesario
- [ ] Revisar logs de seguridad

## ✅ Monitoreo

### Configurar monitoreo:
- [ ] Configurar alertas de error
- [ ] Configurar monitoreo de uptime
- [ ] Configurar logs de acceso
- [ ] Documentar procedimientos de mantenimiento

## ✅ Documentación

### Finalizar documentación:
- [ ] Documentar credenciales de acceso
- [ ] Documentar proceso de actualización
- [ ] Documentar proceso de backup
- [ ] Crear guía de mantenimiento

## 🚨 Solución de Problemas Comunes

### Error 500:
- [ ] Verificar permisos de archivos
- [ ] Revisar logs de error
- [ ] Verificar configuración de `.env`
- [ ] Verificar que PHP tiene las extensiones necesarias

### Error de Base de Datos:
- [ ] Verificar credenciales en `.env`
- [ ] Confirmar que la base de datos existe
- [ ] Verificar permisos del usuario de BD
- [ ] Probar conexión manualmente

### Assets no Cargan:
- [ ] Verificar que `npm run build` se ejecutó
- [ ] Verificar rutas en `vite.config.js`
- [ ] Limpiar cache del navegador
- [ ] Verificar que el archivo `manifest.json` existe

### Problemas de Permisos:
- [ ] Verificar propietario de archivos
- [ ] Verificar permisos de directorios
- [ ] Verificar que el usuario web puede escribir
- [ ] Revisar logs de error del servidor

## 📞 Contacto de Soporte

Si encuentras problemas:
1. Revisar logs de error
2. Verificar configuraciones
3. Consultar documentación de Laravel
4. Contactar soporte de Hostinger
5. Revisar foros de la comunidad

---

**Nota**: Marca cada elemento como completado (✅) conforme avances en el proceso de despliegue. 