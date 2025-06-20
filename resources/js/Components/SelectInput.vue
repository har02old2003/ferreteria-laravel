<template>
  <select
    :value="modelValue"
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
    @change="$emit('update:modelValue', $event.target.value)"
  >
    <option value="">Seleccionar opción</option>
    <option
      v-for="option in options"
      :key="option.id"
      :value="option.id"
    >
      {{ option.name }}
    </option>
  </select>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  options: {
    type: Array,
    required: true
  }
});

defineEmits(['update:modelValue']);
</script> 