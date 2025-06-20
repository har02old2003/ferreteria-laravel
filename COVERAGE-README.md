# 📊 Cobertura de Código - Sistema de Ferretería

Este documento explica cómo configurar y usar la cobertura de código en el proyecto.

## 🚀 Comandos Disponibles

### Ejecutar Pruebas Básicas
```bash
# Ejecutar todas las pruebas
composer test

# Ejecutar pruebas específicas
php artisan test --filter=ProductTest
```

### Ejecutar Pruebas con Cobertura
```bash
# Cobertura en consola
composer test-coverage

# Generar reporte HTML
composer test-coverage-html

# Generar reporte Clover (XML)
composer test-coverage-clover
```

## 📋 Requisitos Previos

Para que funcione la cobertura de código necesitas instalar una extensión PHP:

### Opción 1: PCOV (Recomendado)

1. Descarga PCOV desde: https://pecl.php.net/package/pcov
2. Selecciona la versión compatible con PHP 8.2 Thread Safe x64
3. Extrae `php_pcov.dll` en tu directorio `PHP/ext/`
4. Agrega `extension=pcov` a tu `php.ini`
5. Reinicia tu servidor web

### Opción 2: Xdebug

1. Descarga Xdebug desde: https://xdebug.org/download
2. Instala según las instrucciones para Windows
3. Configura en tu `php.ini`

## 📊 Interpretar los Resultados

### Colores en Reporte HTML

- **Verde**: Líneas cubiertas por pruebas
- **Rojo**: Líneas NO cubiertas por pruebas
- **Amarillo**: Líneas parcialmente cubiertas

### Métricas de Cobertura

- **≥ 80%**: Excelente cobertura
- **50-79%**: Buena cobertura
- **< 50%**: Necesita mejoras

## 🎯 Estado Actual

El proyecto tiene **87 pruebas** que cubren:

- ✅ Autenticación
- ✅ Productos
- ✅ Ventas
- ✅ Clientes
- ✅ Dashboard
- ✅ Roles y permisos

## 📁 Archivos Generados

```
coverage-html/          # Reporte HTML interactivo
coverage.xml           # Reporte XML (Clover)
tests/results.xml      # Resultados de pruebas
```

## 🔧 Configuración Actual

La configuración de cobertura está en:

- `phpunit.xml` - Configuración principal
- `composer.json` - Scripts personalizados
- `.gitignore` - Excluye archivos de cobertura

## 📈 Mejores Prácticas

1. **Mantén cobertura ≥ 80%** en código crítico
2. **Escribe pruebas para nuevas características**
3. **Revisa reportes regularmente**
4. **Enfócate en lógica de negocio**

## 🚨 Solución de Problemas

Si ves "No code coverage driver available":

1. Verifica que PCOV o Xdebug estén instalados
2. Ejecuta: `php -m | findstr -i pcov`
3. Revisa tu configuración `php.ini`
4. Reinicia tu servidor web

## 📞 Soporte

Para problemas específicos:

1. Revisa la documentación de Laravel Testing
2. Consulta la documentación de Pest PHP
3. Verifica la configuración de tu servidor local 