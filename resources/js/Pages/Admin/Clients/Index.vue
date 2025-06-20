<template>
    <Head title="Gestión de Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Clientes
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona los clientes de la ferretería</p>
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
                                <p class="text-blue-100 text-sm font-medium">Total Clientes</p>
                                <p class="text-2xl font-bold">{{ safeClients.length }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <UserGroupIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Clientes Activos</p>
                                <p class="text-2xl font-bold">{{ activeClients }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CheckCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Nuevos Este Mes</p>
                                <p class="text-2xl font-bold">{{ newClientsThisMonth }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ChartBarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Compras</p>
                                <p class="text-2xl font-bold">{{ totalPurchases }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CurrencyDollarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Clientes -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <UserGroupIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Lista de Clientes
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ safeClients.length }} clientes registrados</span>
                                <button
                                    @click="openCreateModal"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                                >
                                    <PlusIcon class="h-4 w-4" />
                                    <span>Nuevo Cliente</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="safeClients.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <UserGroupIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay clientes registrados aún</p>
                            <p class="text-sm text-gray-400 mt-1">Comienza agregando tu primer cliente</p>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Crear Primer Cliente
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
                                        placeholder="Buscar clientes..."
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
                                                Cliente
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                DNI
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Teléfono
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Email
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
                                        <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4 text-sm font-medium text-gray-900">
                                                {{ client.formatted_id || 'CL' + String(client.id).padStart(4, '0') }}
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="text-sm font-medium text-gray-900">{{ client.first_name }} {{ client.last_name }}</div>
                                                <div class="text-xs text-gray-500">{{ client.address || 'Sin dirección' }}</div>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ client.dni || 'Sin DNI' }}
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ client.phone || 'Sin teléfono' }}
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ client.email || 'Sin email' }}
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                        client.status === 'active' 
                                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                            : 'bg-red-50 text-red-700 ring-red-600/20'
                                                    ]"
                                                >
                                                    <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                                                        client.status === 'active' ? 'bg-green-600' : 'bg-red-600'
                                                    ]" />
                                                    {{ client.status === 'active' ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button
                                                        @click="viewClientDetails(client)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                        :title="`Ver detalles de ${client.first_name} ${client.last_name}`"
                                                    >
                                                        <EyeIcon class="h-3 w-3 mr-1" />
                                                        Ver
                                                    </button>
                                                    <button
                                                        @click="openEditModal(client)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                                        :title="`Editar ${client.first_name} ${client.last_name}`"
                                                    >
                                                        <PencilIcon class="h-3 w-3 mr-1" />
                                                        Editar
                                                    </button>
                                                    <button
                                                        @click="confirmDelete(client)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        :title="`Eliminar ${client.first_name} ${client.last_name}`"
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
            max-width="3xl"
            @close="closeModal"
        >
            <div class="bg-white">
                <!-- Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
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
                            <!-- Buscar por DNI -->
                            <div v-if="!isEditing" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <TextInput
                                        v-model="dniSearch"
                                        type="text"
                                        placeholder="Buscar por DNI"
                                        class="flex-1"
                                        maxlength="8"
                                    />
                                    <ActionButton
                                        text="Buscar"
                                        :loading="searchingDni"
                                        :disabled="searchingDni || dniSearch.length !== 8"
                                        @click="searchByDni"
                                    />
                                </div>
                                <p class="text-xs text-blue-600 mt-2">Ingresa el DNI para buscar automáticamente los datos del cliente</p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel for="first_name" value="Nombres" />
                                    <TextInput
                                        id="first_name"
                                        v-model="form.first_name"
                                        type="text"
                                        class="mt-2 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.first_name" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="last_name" value="Apellidos" />
                                    <TextInput
                                        id="last_name"
                                        v-model="form.last_name"
                                        type="text"
                                        class="mt-2 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.last_name" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <InputLabel for="dni" value="DNI" />
                                    <TextInput
                                        id="dni"
                                        v-model="form.dni"
                                        type="text"
                                        class="mt-2 block w-full"
                                        maxlength="8"
                                        required
                                    />
                                    <InputError :message="form.errors.dni" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="phone" value="Teléfono" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-2 block w-full"
                                    />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="email" value="Correo Electrónico" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-2 block w-full"
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="address" value="Dirección" />
                                <TextArea
                                    id="address"
                                    v-model="form.address"
                                    class="mt-2 block w-full"
                                    rows="3"
                                />
                                <InputError :message="form.errors.address" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="status" value="Estado" />
                                <div class="mt-2">
                                    <Toggle
                                        id="status"
                                        v-model="form.status"
                                        :label="form.status === 'active' ? 'Activo' : 'Inactivo'"
                                    />
                                </div>
                                <InputError :message="form.errors.status" class="mt-2" />
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
                        ¿Estás seguro de que deseas eliminar este cliente? Esta acción no se puede deshacer.
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
                            @click="deleteClient"
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
                            Detalles del Cliente
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
                <div class="px-6 py-6" v-if="selectedClient">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre completo:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedClient.first_name }} {{ selectedClient.last_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">DNI:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedClient.dni || 'Sin DNI' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Teléfono:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedClient.phone || 'Sin teléfono' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedClient.email || 'Sin email' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Dirección:</label>
                            <p class="text-sm text-gray-900 mt-1">{{ selectedClient.address || 'Sin dirección' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Estado:</label>
                            <div class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        selectedClient.status === 'active' 
                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                            : 'bg-red-50 text-red-700 ring-red-600/20'
                                    ]"
                                >
                                    {{ selectedClient.status === 'active' ? 'Activo' : 'Inactivo' }}
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
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon, CubeIcon, CheckCircleIcon, ChartBarIcon, DocumentTextIcon, MagnifyingGlassIcon, UserGroupIcon, XMarkIcon } from '@heroicons/vue/24/outline'
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
    clients: {
        type: Array,
        required: true
    }
})

const columns = [
    { key: 'formatted_id', label: 'ID' },
    { key: 'full_name', label: 'Nombre' },
    { key: 'dni', label: 'DNI' },
    { key: 'phone', label: 'Teléfono' },
    { key: 'email', label: 'Email' },
    { key: 'status', label: 'Estado' },
    { key: 'actions', label: 'Acciones' }
]

const showModal = ref(false)
const showDeleteModal = ref(false)
const showDetailsModal = ref(false)
const isEditing = ref(false)
const selectedClient = ref(null)


const searchQuery = ref('')

const form = useForm({
    first_name: '',
    last_name: '',
    dni: '',
    phone: '',
    email: '',
    address: '',
    status: 'active'
})

const deleteForm = useForm({})
const { success, error } = useNotifications()

// Computed properties para estadísticas
const safeClients = computed(() => {
    return Array.isArray(props.clients) ? props.clients : []
})

const activeClients = computed(() => {
    return safeClients.value.filter(client => client.status === 'active').length
})

const newClientsThisMonth = computed(() => {
    const currentMonth = new Date().getMonth()
    const currentYear = new Date().getFullYear()
    return safeClients.value.filter(client => {
        const clientDate = new Date(client.created_at)
        return clientDate.getMonth() === currentMonth && clientDate.getFullYear() === currentYear
    }).length
})

const totalPurchases = computed(() => {
    return safeClients.value.reduce((total, client) => {
        return total + (client.total_purchases || 0)
    }, 0)
})

const filteredClients = computed(() => {
    const query = searchQuery.value.toLowerCase()
    return safeClients.value.filter(client => {
        return (
            client.first_name.toLowerCase().includes(query) ||
            client.last_name.toLowerCase().includes(query) ||
            client.dni.toLowerCase().includes(query) ||
            client.phone.toLowerCase().includes(query) ||
            client.email.toLowerCase().includes(query) ||
            client.address.toLowerCase().includes(query)
        )
    })
})

const handleSearch = () => {
    // Implementar búsqueda si es necesario
}

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    form.status = 'active'
    showModal.value = true
}

const openEditModal = (client) => {
    isEditing.value = true
    selectedClient.value = client
    form.first_name = client.first_name
    form.last_name = client.last_name
    form.dni = client.dni
    form.phone = client.phone
    form.email = client.email
    form.address = client.address
    form.status = client.status
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
    
}

const confirmDelete = (client) => {
    selectedClient.value = client
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedClient.value = null
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.clients.update', selectedClient.value.id), {
            onSuccess: () => {
                closeModal()
                success('Cliente actualizado', 'El cliente se ha actualizado correctamente')
            },
            onError: () => {
                error('Error al actualizar', 'No se pudo actualizar el cliente')
            }
        })
    } else {
        form.post(route('admin.clients.store'), {
            onSuccess: () => {
                closeModal()
                success('Cliente creado', 'El cliente se ha creado correctamente')
            },
            onError: () => {
                error('Error al crear', 'No se pudo crear el cliente')
            }
        })
    }
}

const deleteClient = () => {
    deleteForm.delete(route('admin.clients.destroy', selectedClient.value.id), {
        onSuccess: () => {
            closeDeleteModal()
            success('Cliente eliminado', 'El cliente se ha eliminado correctamente')
        },
        onError: () => {
            error('Error al eliminar', 'No se pudo eliminar el cliente')
        }
    })
}

const viewClientDetails = (client) => {
    selectedClient.value = client
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedClient.value = null
}
</script> 