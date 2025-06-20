<template>
    <div class="flex items-center">
        <button
            type="button"
            :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                modelValue ? 'bg-indigo-600' : 'bg-gray-200'
            ]"
            role="switch"
            :aria-checked="modelValue"
            @click="$emit('update:modelValue', !modelValue)"
        >
            <span
                :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    modelValue ? 'translate-x-5' : 'translate-x-0'
                ]"
            />
        </button>
        <span v-if="label" class="ml-3 text-sm">
            {{ label }}
        </span>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        required: true
    },
    label: {
        type: String,
        default: ''
    }
})

defineEmits(['update:modelValue'])
</script> 