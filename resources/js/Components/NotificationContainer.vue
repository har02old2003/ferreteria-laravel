<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <TransitionGroup
            name="notification"
            tag="div"
            class="space-y-2"
        >
            <Notification
                v-for="notification in notifications"
                :key="notification.id"
                :show="notification.show"
                :type="notification.type"
                :title="notification.title"
                :message="notification.message"
                @close="removeNotification(notification.id)"
            />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { useNotifications } from '@/Composables/useNotifications'
import Notification from '@/Components/Notification.vue'

const { notifications, removeNotification } = useNotifications()
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.notification-leave-to {
    opacity: 0;
    transform: translateX(100%);
}

.notification-move {
    transition: transform 0.3s ease;
}
</style> 