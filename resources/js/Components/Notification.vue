<template>
    <div
        v-if="show"
        class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
        :class="typeClasses"
    >
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <CheckCircleIcon v-if="type === 'success'" class="h-6 w-6 text-green-400" aria-hidden="true" />
                    <ExclamationTriangleIcon v-else-if="type === 'warning'" class="h-6 w-6 text-yellow-400" aria-hidden="true" />
                    <XCircleIcon v-else-if="type === 'error'" class="h-6 w-6 text-red-400" aria-hidden="true" />
                    <InformationCircleIcon v-else class="h-6 w-6 text-blue-400" aria-hidden="true" />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">{{ title }}</p>
                    <p v-if="message" class="mt-1 text-sm text-gray-500">{{ message }}</p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button
                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        @click="$emit('close')"
                    >
                        <span class="sr-only">Cerrar</span>
                        <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { CheckCircleIcon, ExclamationTriangleIcon, XCircleIcon, InformationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        default: ''
    }
})

defineEmits(['close'])

const typeClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'border-l-4 border-green-400'
        case 'error':
            return 'border-l-4 border-red-400'
        case 'warning':
            return 'border-l-4 border-yellow-400'
        case 'info':
            return 'border-l-4 border-blue-400'
        default:
            return 'border-l-4 border-green-400'
    }
})
</script> 