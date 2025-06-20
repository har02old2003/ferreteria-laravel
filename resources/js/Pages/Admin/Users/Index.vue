<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Usuarios
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona los usuarios del sistema</p>
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
                                <p class="text-blue-100 text-sm font-medium">Total Usuarios</p>
                                <p class="text-2xl font-bold">{{ safeUsers.length }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <UserGroupIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Usuarios Activos</p>
                                <p class="text-2xl font-bold">{{ activeUsers }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CheckCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Administradores</p>
                                <p class="text-2xl font-bold">{{ adminUsers }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ShieldCheckIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Empleados</p>
                                <p class="text-2xl font-bold">{{ employeeUsers }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <UserIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Usuarios -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <UserGroupIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Lista de Usuarios
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ safeUsers.length }} usuarios registrados</span>
                                <button
                                    @click="openCreateModal"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                                >
                                    <PlusIcon class="h-4 w-4" />
                                    <span>Nuevo Usuario</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="safeUsers.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <UserGroupIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay usuarios registrados aún</p>
                            <p class="text-sm text-gray-400 mt-1">Comienza agregando tu primer usuario</p>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Crear Primer Usuario
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
                                        placeholder="Buscar usuarios..."
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
                                                Usuario
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Rol
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Fecha de Registro
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4 text-sm font-medium text-gray-900">
                                                {{ user.formatted_id || 'U' + String(user.id).padStart(4, '0') }}
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <img
                                                        :src="user.profile_photo_url || '/default-avatar.png'"
                                                        :alt="user.name"
                                                        class="h-8 w-8 rounded-full mr-3"
                                                    />
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                        user.role === 'admin' 
                                                            ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20'
                                                            : 'bg-blue-50 text-blue-700 ring-blue-600/20'
                                                    ]"
                                                >
                                                    {{ user.role === 'admin' ? 'Administrador' : 'Empleado' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ new Date(user.created_at).toLocaleDateString('es-ES') }}
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button
                                                        @click="viewUserDetails(user)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                        :title="`Ver detalles de ${user.name}`"
                                                    >
                                                        <EyeIcon class="h-3 w-3 mr-1" />
                                                        Ver
                                                    </button>
                                                    <button
                                                        @click="openEditModal(user)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                                        :title="`Editar ${user.name}`"
                                                    >
                                                        <PencilIcon class="h-3 w-3 mr-1" />
                                                        Editar
                                                    </button>
                                                    <button
                                                        v-if="user.id !== $page.props.auth.user.id"
                                                        @click="confirmDelete(user)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        :title="`Eliminar ${user.name}`"
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
            :title="isEditing ? 'Editar Usuario' : 'Nuevo Usuario'"
            @close="closeModal"
        >
            <form @submit.prevent="submitForm">
                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nombre" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Correo Electrónico" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div v-if="!isEditing">
                        <InputLabel for="password" value="Contraseña" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div v-if="!isEditing">
                        <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Rol" />
                        <SelectInput
                            id="role"
                            v-model="form.role"
                            :options="roleOptions"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
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
        </Modal>

        <!-- Modal de Confirmación de Eliminación -->
        <Modal
            :show="showDeleteModal"
            title="Confirmar Eliminación"
            @close="closeDeleteModal"
        >
            <div class="p-6">
                <p class="text-sm text-gray-600">
                    ¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.
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
                        @click="deleteUser"
                    />
                </div>
            </div>
        </Modal>

        <!-- Modal de Detalles del Usuario -->
        <UserDetailsModal
            :show="showDetailsModal"
            :user="selectedUserWithFormattedId"
            :sales="userSales"
            :statistics="userStatistics"
            :loading="loadingSales"
            @close="closeDetailsModal"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon, UserGroupIcon, CheckCircleIcon, ShieldCheckIcon, UserIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useNotifications } from '@/Composables/useNotifications'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import UserDetailsModal from '@/Components/UserDetailsModal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    users: {
        type: Array,
        required: true
    }
})

const columns = [
    { key: 'formatted_id', label: 'ID' },
    { key: 'name', label: 'Usuario' },
    { key: 'role', label: 'Rol' },
    { key: 'created_at', label: 'Fecha Registro' },
    { key: 'actions', label: 'Acciones' }
]

const roleOptions = [
    { id: 'admin', name: 'Administrador' },
    { id: 'employee', name: 'Empleado' }
]

const showModal = ref(false)
const showDeleteModal = ref(false)
const showDetailsModal = ref(false)
const isEditing = ref(false)
const selectedUser = ref(null)
const userSales = ref([])
const userStatistics = ref({
    total_sales: 0,
    total_revenue: 0,
    completed_sales: 0,
    average_sale: 0
})
const loadingSales = ref(false)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'employee'
})

const deleteForm = useForm({})
const { success, error } = useNotifications()

// Computed properties para estadísticas
const safeUsers = computed(() => {
    return Array.isArray(props.users) ? props.users : []
})

const activeUsers = computed(() => {
    return safeUsers.value.length // Todos los usuarios son activos
})

const adminUsers = computed(() => {
    return safeUsers.value.filter(user => user.role === 'admin').length
})

const employeeUsers = computed(() => {
    return safeUsers.value.filter(user => user.role === 'employee').length
})

const searchQuery = ref('')

const filteredUsers = computed(() => {
    return safeUsers.value.filter(user => {
        const query = searchQuery.value.toLowerCase()
        return (
            user.name.toLowerCase().includes(query) ||
            user.email.toLowerCase().includes(query)
        )
    })
})

const selectedUserWithFormattedId = computed(() => {
    if (!selectedUser.value) return null
    return {
        ...selectedUser.value,
        formatted_id: 'U' + String(selectedUser.value.id).padStart(4, '0')
    }
})

const handleSearch = (query) => {
    searchQuery.value = query
}

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    form.role = 'employee'
    showModal.value = true
}

const openEditModal = (user) => {
    isEditing.value = true
    selectedUser.value = user
    form.name = user.name
    form.email = user.email
    form.role = user.role
    form.password = ''
    form.password_confirmation = ''
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

const confirmDelete = (user) => {
    selectedUser.value = user
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedUser.value = null
}

const submitForm = () => {
    if (isEditing.value) {
        const updateData = {
            name: form.name,
            email: form.email,
            role: form.role
        }
        
        form.put(route('admin.users.update', selectedUser.value.id), {
            data: updateData,
            onSuccess: () => {
                closeModal()
                success('Usuario actualizado', 'El usuario se ha actualizado correctamente')
            },
            onError: () => {
                error('Error al actualizar', 'No se pudo actualizar el usuario')
            }
        })
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                closeModal()
                success('Usuario creado', 'El usuario se ha creado correctamente')
            },
            onError: () => {
                error('Error al crear', 'No se pudo crear el usuario')
            }
        })
    }
}

const deleteUser = () => {
    deleteForm.delete(route('admin.users.destroy', selectedUser.value.id), {
        onSuccess: () => {
            closeDeleteModal()
            success('Usuario eliminado', 'El usuario se ha eliminado correctamente')
        },
        onError: () => {
            error('Error al eliminar', 'No se pudo eliminar el usuario')
        }
    })
}

const loadUserSales = async (userId) => {
    loadingSales.value = true
    try {
        // Usar el nuevo endpoint específico para datos JSON
        const response = await fetch(route('admin.users.data', userId), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        
        if (!response.ok) {
            throw new Error('Failed to fetch user data')
        }
        
        const data = await response.json()
        
        userSales.value = data.sales || []
        userStatistics.value = data.statistics || {
            total_sales: 0,
            total_revenue: 0,
            completed_sales: 0,
            average_sale: 0
        }
        
        // Actualizar también los datos del usuario seleccionado
        if (data.user) {
            selectedUser.value = {
                ...selectedUser.value,
                ...data.user
            }
        }
        
    } catch (error) {
        console.error('Error loading user sales:', error)
        error('Error', 'No se pudieron cargar las ventas del usuario')
    } finally {
        loadingSales.value = false
    }
}

const viewUserDetails = (user) => {
    selectedUser.value = user
    loadUserSales(user.id)
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedUser.value = null
    userSales.value = []
    userStatistics.value = {
        total_sales: 0,
        total_revenue: 0,
        completed_sales: 0,
        average_sale: 0
    }
}
</script> 