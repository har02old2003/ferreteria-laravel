import { ref, reactive } from 'vue'

const notifications = ref([])

export function useNotifications() {
    const addNotification = (notification) => {
        const id = Date.now()
        const newNotification = {
            id,
            show: true,
            ...notification
        }
        
        notifications.value.push(newNotification)
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            removeNotification(id)
        }, 5000)
        
        return id
    }
    
    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }
    
    const success = (title, message = '') => {
        return addNotification({
            type: 'success',
            title,
            message
        })
    }
    
    const error = (title, message = '') => {
        return addNotification({
            type: 'error',
            title,
            message
        })
    }
    
    const warning = (title, message = '') => {
        return addNotification({
            type: 'warning',
            title,
            message
        })
    }
    
    const info = (title, message = '') => {
        return addNotification({
            type: 'info',
            title,
            message
        })
    }
    
    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info
    }
} 