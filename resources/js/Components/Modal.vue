<script setup>
defineProps({
  show: Boolean,
  maxWidth: String
})
</script>

<template>
  <teleport to="body">
    <div v-if="show" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl relative p-6">
        <div style="font-size:2rem;color:red;text-align:center;">MODAL ABIERTO</div>
        <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl">
          &times;
        </button>
        <slot />
      </div>
    </div>
  </teleport>
</template>
