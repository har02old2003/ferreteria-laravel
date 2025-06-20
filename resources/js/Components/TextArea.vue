<template>
    <textarea
        :value="modelValue"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        @input="$emit('update:modelValue', $event.target.value)"
    ></textarea>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: ''
    }
});

defineEmits(['update:modelValue']);
</script>
