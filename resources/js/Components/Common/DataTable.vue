<template>
  <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
    <!-- Barra de búsqueda -->
    <div class="border-b border-gray-200 p-4">
      <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
          <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </div>
        <input
          type="text"
          :placeholder="searchPlaceholder"
          class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
          v-model="searchQuery"
          @input="handleSearch"
        />
      </div>
    </div>

    <!-- Tabla -->
    <div class="flow-root overflow-hidden">
      <div class="overflow-x-auto">
        <div class="inline-block min-w-full align-middle">
          <table class="min-w-full divide-y divide-gray-300 table-fixed w-full">
            <thead>
              <tr>
                <th
                  v-for="column in columns"
                  :key="column.key"
                  scope="col"
                  :class="[
                    'px-3 py-3.5 text-left text-sm font-semibold text-gray-900',
                    getColumnWidth(column.key)
                  ]"
                >
                  {{ column.label }}
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-if="!items || items.length === 0">
                <td
                  :colspan="columns.length"
                  class="px-3 py-4 text-sm text-center text-gray-500"
                >
                  No hay registros disponibles
                </td>
              </tr>
              <tr
                v-for="item in items"
                :key="item.id"
                class="hover:bg-gray-50"
              >
                <td
                  v-for="column in columns"
                  :key="column.key"
                  :class="[
                    'px-3 py-4 text-sm text-gray-900',
                    getColumnWidth(column.key),
                    column.key === 'actions' ? '' : 'truncate'
                  ]"
                >
                  <slot
                    :name="column.key"
                    :item="item"
                  >
                    <span class="truncate block">{{ item[column.key] }}</span>
                  </slot>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Paginación -->
    <div v-if="items.links" class="border-t border-gray-200">
      <Pagination :links="items.links" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  columns: {
    type: Array,
    required: true
  },
  items: {
    type: [Array, Object],
    required: true
  },
  searchPlaceholder: {
    type: String,
    default: 'Buscar...'
  },
  initialSearch: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:search'])

const searchQuery = ref(props.initialSearch)
let searchTimeout = null

const handleSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    emit('update:search', searchQuery.value)
  }, 300)
}

// Actualizar la búsqueda cuando cambie el valor inicial
watch(() => props.initialSearch, (newValue) => {
  searchQuery.value = newValue
})

const getColumnWidth = (key) => {
  const widthMap = {
    'image': 'w-12',
    'name': 'w-1/3',
    'category': 'w-1/6',
    'sku': 'w-1/8',
    'price': 'w-1/8',
    'stock': 'w-1/8',
    'status': 'w-1/8',
    'actions': 'w-1/6',
    // Anchos por defecto para otras tablas
    'email': 'w-1/3',
    'id': 'w-16',
    'code': 'w-1/8',
    'total': 'w-1/8'
  }
  
  return widthMap[key] || 'w-auto'
}
</script> 