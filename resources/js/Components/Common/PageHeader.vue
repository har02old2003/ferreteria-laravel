<template>
  <div class="bg-white shadow">
    <div class="px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">
            {{ title }}
          </h1>
          <p v-if="description" class="mt-2 text-sm text-gray-600">
            {{ description }}
          </p>
          <div v-if="$slots.breadcrumbs" class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <slot name="breadcrumbs" />
          </div>
        </div>
        <div class="flex items-center gap-4">
          <slot name="actions" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  }
});
</script>
