# 📈 Sistema de Métricas - Ferretería Laravel

Este documento explica las **3 métricas críticas** implementadas en el sistema de ferretería para monitorear la calidad y performance del software.

## 🎯 Métricas Implementadas

### 1. 📊 **Cobertura de Pruebas (Test Coverage)**

**¿Qué mide?** Porcentaje del código cubierto por pruebas automatizadas.

**Comandos disponibles:**

```bash
# Generar cobertura HTML
composer test-coverage-html

# Generar cobertura XML
composer test-coverage-clover

# Reporte detallado
php scripts/coverage-report.php
```

**Métricas clave:**

- **Cobertura de líneas**: % de líneas ejecutadas por pruebas
- **Cobertura de métodos**: % de métodos probados
- **Cobertura de clases**: % de clases con pruebas

**Objetivos:**

- ✅ **≥ 80%**: Excelente cobertura
- ⚠️ **50-79%**: Buena cobertura
- ❌ **< 50%**: Necesita mejoras

### 2. 🔧 **Mean Time Between Failures (MTBF)**

**¿Qué mide?** Tiempo promedio entre fallos del sistema para medir confiabilidad.

**Tipos de fallos monitoreados:**

- Errores del sistema
- Errores de base de datos
- Errores de validación
- Errores de red
- Errores de autenticación

**Comandos disponibles:**

```bash
# Generar reporte MTBF (30 días)
php artisan metrics:generate --mtbf

# Reporte personalizado (7 días)
php artisan metrics:generate --mtbf --days=7
```

**Métricas clave:**

- **MTBF en días/horas**: Tiempo entre fallos
- **Disponibilidad**: % de tiempo operativo
- **Tipos de fallo**: Desglose por categoría
- **Tendencia**: Mejorando/Estable/Empeorando

**Objetivos:**

- ✅ **≥ 30 días**: Excelente confiabilidad
- 🟢 **14-29 días**: Buena confiabilidad
- ⚠️ **7-13 días**: Regular
- ❌ **< 7 días**: Crítico

### 3. ⚡ **Velocidad del Equipo (Agile Velocity)**

**¿Qué mide?** Story points completados por sprint para planificación ágil.

**Comandos disponibles:**

```bash
# Generar reporte de velocidad
php artisan metrics:generate --velocity

# Análisis personalizado (10 sprints)
php artisan metrics:generate --velocity --sprints=10
```

**Métricas clave:**

- **Velocidad promedio**: Story points por sprint
- **Consistencia**: Estabilidad de la velocidad
- **Tendencia**: Mejorando/Estable/Declinando
- **Predicciones**: Capacidad para próximo sprint

**Objetivos:**

- 🎯 **Consistencia ≥ 80%**: Equipo predecible
- 📈 **Tendencia positiva**: Mejora continua
- 🔮 **Predicciones confiables**: Mejor planificación

## 🚀 Uso Rápido

### Generar todos los reportes:

```bash
php artisan metrics:generate --all
```

### Comandos individuales:

```bash
# Solo cobertura
php artisan metrics:generate --coverage

# Solo MTBF
php artisan metrics:generate --mtbf

# Solo velocidad ágil
php artisan metrics:generate --velocity
```

## 📁 Archivos Generados

```
coverage-html/          # Reporte HTML de cobertura
coverage.xml           # Reporte XML de cobertura
coverage-report.json   # Datos de cobertura
mtbf-report.json       # Reporte MTBF
velocity-report.json   # Reporte de velocidad
```

## 🛠️ Configuración Inicial

### 1. Ejecutar migraciones:

```bash
php artisan migrate
```

### 2. Registrar primer fallo (ejemplo):

```php
$mtbfService = new MTBFService();
$mtbfService->recordFailure(
    'system_error', 
    'Error en conexión de base de datos',
    ['module' => 'products', 'user_id' => 123]
);
```

### 3. Registrar historia completada (ejemplo):

```php
$velocityService = new AgileVelocityService();
$velocityService->recordStoryCompleted(
    'STORY-001',
    'Implementar búsqueda de productos',
    8,  // story points
    'Sprint-1',
    'desarrollador@empresa.com'
);
```

## 📊 Dashboard de Métricas

Para ver todas las métricas en un solo lugar:

```bash
# Generar reporte completo
php artisan metrics:generate --all

# Ver archivos JSON generados
ls -la *-report.json
```

## 🔍 Interpretación de Resultados

### Cobertura de Código

- **Verde**: Líneas/métodos cubiertos por pruebas
- **Rojo**: Código sin probar (requiere atención)
- **Amarillo**: Cobertura parcial

### MTBF

- **EXCELENTE (≥30d)**: Sistema muy confiable
- **BUENO (14-29d)**: Confiabilidad aceptable
- **REGULAR (7-13d)**: Necesita mejoras
- **CRÍTICO (<7d)**: Requiere acción inmediata

### Velocidad Ágil

- **EXCELENTE**: Velocidad consistente y predecible
- **BUENA**: Algunas variaciones menores
- **REGULAR**: Inconsistencias notables
- **INCONSISTENTE**: Velocidad muy variable

## 📈 Mejores Prácticas

### Para Cobertura:

1. Mantener cobertura ≥ 80% en código crítico
2. Escribir pruebas antes de implementar (TDD)
3. Revisar reportes HTML regularmente
4. Enfocar en lógica de negocio compleja

### Para MTBF:

1. Registrar todos los errores significativos
2. Clasificar fallos por severidad
3. Implementar alertas proactivas
4. Documentar resoluciones

### Para Velocidad Ágil:

1. Estimar story points consistentemente
2. Completar sprints según planificado
3. Revisar velocidad en retrospectivas
4. Usar predicciones para planificación

## 🚨 Alertas y Umbrales

El sistema puede configurarse para enviar alertas cuando:

- Cobertura cae por debajo del 70%
- MTBF es menor a 7 días
- Velocidad del equipo declina > 20%

## 📞 Soporte

Para problemas o mejoras:

1. Revisar logs en `storage/logs/`
2. Verificar configuración de base de datos
3. Ejecutar `php artisan config:clear`
4. Contactar al equipo de desarrollo 