import { ref, reactive } from 'vue'

const notifications = ref([])
let nextId = 1

export function useNotifications() {
    const addNotification = (options) => {
        const notification = reactive({
            id: nextId++,
            title: options.title || 'Notificación',
            message: options.message || '',
            type: options.type || 'info',
            autoDismiss: options.autoDismiss !== false,
            duration: options.duration || 5000,
            action: options.action || null,
            progress: 0,
            remainingTime: options.duration || 5000,
            show: true
        })

        notifications.value.push(notification)

        if (notification.autoDismiss) {
            // Animar la barra de progreso
            setTimeout(() => {
                notification.progress = 100
            }, 100)

            // Auto-remover después del duration
            setTimeout(() => {
                removeNotification(notification.id)
            }, notification.duration)
        }

        return notification.id
    }

    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }

    const clearAll = () => {
        notifications.value = []
    }

    // Métodos de conveniencia
    const success = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'success'
        })
    }

    const error = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'error'
        })
    }

    const warning = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'warning'
        })
    }

    const info = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'info'
        })
    }

    const sales = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'sales'
        })
    }

    const analytics = (title, message, options = {}) => {
        return addNotification({
            ...options,
            title,
            message,
            type: 'analytics'
        })
    }

    // Función para mostrar notificaciones de dashboard automáticamente
    const showDashboardNotifications = (dashboardData) => {
        // Retraso inicial para efecto dramático
        setTimeout(() => {
            // Notificación de bienvenida
            info('Sistema Cargado', '¡Dashboard actualizado con datos en tiempo real!', {
                duration: 4000
            })
        }, 2000)

        // Notificación de ventas si hay crecimiento
        if (dashboardData.totals?.sales?.percentage > 0) {
            setTimeout(() => {
                sales(
                    '📈 Crecimiento en Ventas',
                    `Las ventas han aumentado ${dashboardData.totals.sales.percentage}% respecto al mes anterior`,
                    { duration: 6000 }
                )
            }, 4500)
        }

        // Notificación de alertas si existen
        const lowStockCount = dashboardData.alerts?.lowStock?.length || 0
        const expiringCount = dashboardData.alerts?.expiring?.length || 0
        
        if (lowStockCount > 0 || expiringCount > 0) {
            setTimeout(() => {
                warning(
                    '⚠️ Alertas del Sistema',
                    `${lowStockCount} productos con stock bajo, ${expiringCount} próximos a vencer`,
                    {
                        duration: 8000,
                        action: {
                            text: 'Ver Detalles',
                            handler: () => {
                                // Scroll hacia la sección de alertas
                                document.querySelector('.alerts-section')?.scrollIntoView({ behavior: 'smooth' })
                            }
                        }
                    }
                )
            }, 7000)
        }

        // Notificación de productos más vendidos (solo para admin)
        if (dashboardData.userRole === 'admin' && dashboardData.topProducts?.length > 0) {
            setTimeout(() => {
                analytics(
                    '🏆 Análisis de Ventas',
                    `${dashboardData.topProducts[0]?.name} es el producto más vendido con ${Math.round(dashboardData.topProducts[0]?.percentage)}% de participación`,
                    { duration: 7000 }
                )
            }, 9500)
        }

        // Notificación de resumen final
        setTimeout(() => {
            success(
                '✅ Sistema Operativo',
                'Todos los módulos funcionando correctamente. ¡Listo para operar!',
                { duration: 5000 }
            )
        }, 12000)
    }

    return {
        notifications,
        addNotification,
        removeNotification,
        clearAll,
        success,
        error,
        warning,
        info,
        sales,
        analytics,
        showDashboardNotifications
    }
} 