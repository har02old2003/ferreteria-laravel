<template>
    <div class="fixed top-20 right-4 z-50 space-y-4">
        <TransitionGroup
            name="notification"
            tag="div"
            enter-active-class="transform transition duration-300 ease-out"
            enter-from-class="translate-x-full opacity-0 scale-95"
            enter-to-class="translate-x-0 opacity-100 scale-100"
            leave-active-class="transform transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100 scale-100"
            leave-to-class="translate-x-full opacity-0 scale-95"
        >
            <div
                v-for="notification in notifications"
                :key="notification.id"
                class="bg-white rounded-xl shadow-xl border border-gray-100 p-4 max-w-sm w-full transform transition-all duration-300 hover:shadow-2xl"
                :class="getNotificationClasses(notification.type)"
            >
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <div :class="getIconContainerClasses(notification.type)">
                            <component
                                :is="getNotificationIcon(notification.type)"
                                class="h-5 w-5"
                                :class="getIconClasses(notification.type)"
                            />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-semibold text-gray-900">
                                {{ notification.title }}
                            </h4>
                            <button
                                @click="removeNotification(notification.id)"
                                class="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <XMarkIcon class="h-4 w-4" />
                            </button>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">
                            {{ notification.message }}
                        </p>
                        <div v-if="notification.action" class="mt-3">
                            <button
                                @click="notification.action.handler"
                                class="text-xs font-medium transition-colors rounded-md px-3 py-1.5"
                                :class="getActionButtonClasses(notification.type)"
                            >
                                {{ notification.action.text }}
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Barra de progreso para auto-dismiss -->
                <div v-if="notification.autoDismiss" class="mt-3">
                    <div class="w-full bg-gray-200 rounded-full h-1 overflow-hidden">
                        <div
                            class="h-full transition-all ease-linear rounded-full"
                            :class="getProgressBarClasses(notification.type)"
                            :style="{
                                width: `${notification.progress || 0}%`,
                                transitionDuration: notification.remainingTime ? `${notification.remainingTime}ms` : '0ms'
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { useNotifications } from '@/Composables/useNotifications'
import { 
    CheckCircleIcon, 
    ExclamationTriangleIcon, 
    InformationCircleIcon, 
    XCircleIcon,
    XMarkIcon,
    BellIcon,
    CurrencyDollarIcon,
    ChartBarIcon
} from '@heroicons/vue/24/outline'

const { notifications, removeNotification } = useNotifications()

const getNotificationIcon = (type) => {
    const icons = {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        error: XCircleIcon,
        info: InformationCircleIcon,
        sales: CurrencyDollarIcon,
        analytics: ChartBarIcon
    }
    return icons[type] || BellIcon
}

const getNotificationClasses = (type) => {
    const classes = {
        success: 'border-l-4 border-green-400 bg-green-50',
        warning: 'border-l-4 border-yellow-400 bg-yellow-50',
        error: 'border-l-4 border-red-400 bg-red-50',
        info: 'border-l-4 border-blue-400 bg-blue-50',
        sales: 'border-l-4 border-emerald-400 bg-emerald-50',
        analytics: 'border-l-4 border-purple-400 bg-purple-50'
    }
    return classes[type] || 'border-l-4 border-gray-400 bg-gray-50'
}

const getIconContainerClasses = (type) => {
    const classes = {
        success: 'p-2 bg-green-100 rounded-lg',
        warning: 'p-2 bg-yellow-100 rounded-lg',
        error: 'p-2 bg-red-100 rounded-lg',
        info: 'p-2 bg-blue-100 rounded-lg',
        sales: 'p-2 bg-emerald-100 rounded-lg',
        analytics: 'p-2 bg-purple-100 rounded-lg'
    }
    return classes[type] || 'p-2 bg-gray-100 rounded-lg'
}

const getIconClasses = (type) => {
    const classes = {
        success: 'text-green-600',
        warning: 'text-yellow-600',
        error: 'text-red-600',
        info: 'text-blue-600',
        sales: 'text-emerald-600',
        analytics: 'text-purple-600'
    }
    return classes[type] || 'text-gray-600'
}

const getActionButtonClasses = (type) => {
    const classes = {
        success: 'bg-green-100 text-green-700 hover:bg-green-200',
        warning: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
        error: 'bg-red-100 text-red-700 hover:bg-red-200',
        info: 'bg-blue-100 text-blue-700 hover:bg-blue-200',
        sales: 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200',
        analytics: 'bg-purple-100 text-purple-700 hover:bg-purple-200'
    }
    return classes[type] || 'bg-gray-100 text-gray-700 hover:bg-gray-200'
}

const getProgressBarClasses = (type) => {
    const classes = {
        success: 'bg-green-400',
        warning: 'bg-yellow-400',
        error: 'bg-red-400',
        info: 'bg-blue-400',
        sales: 'bg-emerald-400',
        analytics: 'bg-purple-400'
    }
    return classes[type] || 'bg-gray-400'
}
</script>

<style scoped>
.notification-move,
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from,
.notification-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.notification-leave-active {
    position: absolute;
    right: 0;
}
</style> 