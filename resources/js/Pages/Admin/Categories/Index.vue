<template>
    <Head title="Gestión de Categorías" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Categorías
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona las categorías de productos</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Categorías</p>
                                <p class="text-2xl font-bold">{{ safeCategories.length }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CubeIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Categorías Activas</p>
                                <p class="text-2xl font-bold">{{ activeCategories }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CheckCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Productos Asociados</p>
                                <p class="text-2xl font-bold">{{ totalProducts }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ChartBarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Promedio Productos</p>
                                <p class="text-2xl font-bold">{{ averageProducts }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <DocumentTextIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Categorías -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <CubeIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Lista de Categorías
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ safeCategories.length }} categorías registradas</span>
                                <button
                                    @click="openCreateModal"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                                >
                                    <PlusIcon class="h-4 w-4" />
                                    <span>Nueva Categoría</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="safeCategories.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <CubeIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay categorías registradas aún</p>
                            <p class="text-sm text-gray-400 mt-1">Comienza agregando tu primera categoría</p>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Crear Primera Categoría
                            </button>
                        </div>

                        <div v-else>
                            <!-- Barra de búsqueda -->
                            <div class="mb-6">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </div>
                                    <input
                                        type="text"
                                        placeholder="Buscar categorías..."
                                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        v-model="searchQuery"
                                        @input="handleSearch"
                                    />
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Nombre
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Descripción
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Productos
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Estado
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4 text-sm font-medium text-gray-900">
                                                {{ category.formatted_id || 'C' + String(category.id).padStart(4, '0') }}
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ category.description || 'Sin descripción' }}
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                                    {{ category.products_count || 0 }} productos
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                        category.is_active 
                                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                            : 'bg-red-50 text-red-700 ring-red-600/20'
                                                    ]"
                                                >
                                                    <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                                                        category.is_active ? 'bg-green-600' : 'bg-red-600'
                                                    ]" />
                                                    {{ category.is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button
                                                        @click="viewCategoryDetails(category)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                        :title="`Ver detalles de ${category.name}`"
                                                    >
                                                        <EyeIcon class="h-3 w-3 mr-1" />
                                                        Ver
                                                    </button>
                                                    <button
                                                        @click="openEditModal(category)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                                        :title="`Editar ${category.name}`"
                                                    >
                                                        <PencilIcon class="h-3 w-3 mr-1" />
                                                        Editar
                                                    </button>
                                                    <button
                                                        @click="confirmDelete(category)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        :title="`Eliminar ${category.name}`"
                                                    >
                                                        <TrashIcon class="h-3 w-3 mr-1" />
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Crear/Editar -->
        <Modal
            :show="showModal"
            max-width="2xl"
            @close="closeModal"
        >
            <div class="bg-white">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}
                        </h3>
                        <button
                            @click="closeModal"
                            class="rounded-md bg-gray-50 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Cerrar</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 py-6">
                    <form @submit.prevent="submitForm">
                        <div class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Nombre" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-2 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Descripción" />
                                <TextArea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-2 block w-full"
                                    rows="4"
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="is_active" value="Estado" />
                                <div class="mt-2">
                                    <Toggle
                                        id="is_active"
                                        v-model="form.is_active"
                                        :label="form.is_active ? 'Activo' : 'Inactivo'"
                                    />
                                </div>
                                <InputError :message="form.errors.is_active" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <ActionButton
                                variant="secondary"
                                text="Cancelar"
                                @click="closeModal"
                            />
                            <ActionButton
                                type="submit"
                                text="Guardar"
                                :loading="form.processing"
                                :disabled="form.processing"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </Modal>

        <!-- Modal de Confirmación de Eliminación -->
        <Modal
            :show="showDeleteModal"
            max-width="md"
            @close="closeDeleteModal"
        >
            <div class="bg-white">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Confirmar Eliminación
                        </h3>
                        <button
                            @click="closeDeleteModal"
                            class="rounded-md bg-gray-50 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Cerrar</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 py-6">
                    <p class="text-sm text-gray-600">
                        ¿Estás seguro de que deseas eliminar esta categoría? Esta acción no se puede deshacer.
                    </p>

                    <div class="mt-6 flex justify-end space-x-3">
                        <ActionButton
                            variant="secondary"
                            text="Cancelar"
                            @click="closeDeleteModal"
                        />
                        <ActionButton
                            variant="danger"
                            text="Eliminar"
                            :loading="deleteForm.processing"
                            :disabled="deleteForm.processing"
                            @click="deleteCategory"
                        />
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Modal de Detalles -->
        <Modal
            :show="showDetailsModal"
            max-width="lg"
            @close="closeDetailsModal"
        >
            <div class="bg-white">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Detalles de la Categoría
                        </h3>
                        <button
                            @click="closeDetailsModal"
                            class="rounded-md bg-gray-50 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Cerrar</span>
                            <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                        </button>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="px-6 py-6" v-if="selectedCategory">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedCategory.name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Descripción:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedCategory.description || 'Sin descripción' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Estado:</label>
                            <div class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        selectedCategory.is_active 
                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                            : 'bg-red-50 text-red-700 ring-red-600/20'
                                    ]"
                                >
                                    {{ selectedCategory.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <ActionButton
                            variant="secondary"
                            text="Cerrar"
                            @click="closeDetailsModal"
                        />
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon, CubeIcon, CheckCircleIcon, ChartBarIcon, DocumentTextIcon, MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useNotifications } from '@/Composables/useNotifications'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import Toggle from '@/Components/Toggle.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
})

const showModal = ref(false)
const showDeleteModal = ref(false)
const showDetailsModal = ref(false)
const isEditing = ref(false)
const selectedCategory = ref(null)
const searchQuery = ref('')

const form = useForm({
    name: '',
    description: '',
    is_active: true
})

const deleteForm = useForm({})
const { success, error } = useNotifications()

// Computed properties para estadísticas
const safeCategories = computed(() => {
    return Array.isArray(props.categories) ? props.categories : []
})

const activeCategories = computed(() => {
    return safeCategories.value.filter(category => category.is_active).length
})

const totalProducts = computed(() => {
    return safeCategories.value.reduce((total, category) => {
        const count = parseInt(category.products_count) || 0
        return total + count
    }, 0)
})

const averageProducts = computed(() => {
    const total = totalProducts.value
    const count = safeCategories.value.length
    return count > 0 ? Math.round(total / count) : 0
})

const filteredCategories = computed(() => {
    if (!searchQuery.value) {
        return safeCategories.value
    }
    
    const query = searchQuery.value.toLowerCase()
    return safeCategories.value.filter(category => 
        category.name.toLowerCase().includes(query) ||
        (category.description && category.description.toLowerCase().includes(query))
    )
})

const handleSearch = () => {
    // La búsqueda se maneja reactivamente a través de filteredCategories
}

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    form.is_active = true
    showModal.value = true
}

const openEditModal = (category) => {
    isEditing.value = true
    selectedCategory.value = category
    form.name = category.name
    form.description = category.description
    form.is_active = category.is_active
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

const confirmDelete = (category) => {
    selectedCategory.value = category
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedCategory.value = null
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.categories.update', selectedCategory.value.id), {
            onSuccess: () => {
                closeModal()
                success('Categoría actualizada', 'La categoría se ha actualizado correctamente')
            },
            onError: () => {
                error('Error al actualizar', 'No se pudo actualizar la categoría')
            }
        })
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => {
                closeModal()
                success('Categoría creada', 'La categoría se ha creado correctamente')
            },
            onError: () => {
                error('Error al crear', 'No se pudo crear la categoría')
            }
        })
    }
}

const deleteCategory = () => {
    deleteForm.delete(route('admin.categories.destroy', selectedCategory.value.id), {
        onSuccess: () => {
            closeDeleteModal()
            success('Categoría eliminada', 'La categoría se ha eliminado correctamente')
        },
        onError: () => {
            error('Error al eliminar', 'No se pudo eliminar la categoría')
        }
    })
}

const viewCategoryDetails = (category) => {
    selectedCategory.value = category
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedCategory.value = null
}
</script> 