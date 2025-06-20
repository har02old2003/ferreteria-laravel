<template>
    <Head title="Proveedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                    <TruckIcon class="h-6 w-6 mr-2 text-gray-600" />
                    Proveedores
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Search and actions -->
                        <div class="mb-6 flex justify-between items-center">
                            <div class="relative w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                </div>
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar proveedores..."
                                    class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Nuevo Proveedor
                            </button>
                        </div>

                        <!-- Suppliers table -->
                        <div class="overflow-x-auto bg-white rounded-lg shadow">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Contacto
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Teléfono
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Productos
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="supplier in filteredSuppliers" :key="supplier.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ supplier.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ supplier.contact_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ supplier.phone }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ supplier.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ supplier.products_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button
                                                @click="toggleSupplierStatus(supplier)"
                                                :class="{
                                                    'px-3 py-1 text-xs font-semibold rounded-full inline-flex items-center': true,
                                                    'bg-green-100 text-green-800': supplier.is_active,
                                                    'bg-red-100 text-red-800': !supplier.is_active
                                                }"
                                            >
                                                <span class="h-2 w-2 rounded-full mr-2" :class="{
                                                    'bg-green-500': supplier.is_active,
                                                    'bg-red-500': !supplier.is_active
                                                }"></span>
                                                {{ supplier.is_active ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <button
                                                    @click="openEditModal(supplier)"
                                                    class="text-indigo-600 hover:text-indigo-900 inline-flex items-center"
                                                >
                                                    <PencilIcon class="h-4 w-4 mr-1" />
                                                    Editar
                                                </button>
                                                <button
                                                    @click="openDeleteModal(supplier)"
                                                    class="text-red-600 hover:text-red-900 inline-flex items-center"
                                                >
                                                    <TrashIcon class="h-4 w-4 mr-1" />
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

        <!-- Create/Edit Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ isEditing ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
                </h2>

                <form @submit.prevent="submitForm" class="mt-6">
                    <div class="grid grid-cols-2 gap-4">
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
                            <InputLabel for="contact_name" value="Persona de Contacto" />
                            <TextInput
                                id="contact_name"
                                v-model="form.contact_name"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.contact_name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="phone" value="Teléfono" />
                            <TextInput
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.phone" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="col-span-2">
                            <InputLabel for="address" value="Dirección" />
                            <TextInput
                                id="address"
                                v-model="form.address"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.address" class="mt-2" />
                        </div>

                        <div class="col-span-2">
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                />
                                <InputLabel for="is_active" value="Activo" class="ml-2" />
                            </div>
                            <InputError :message="form.errors.is_active" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="closeModal" class="mr-3">
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirmar eliminación
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    ¿Está seguro de que desea eliminar este proveedor? Esta acción no se puede deshacer.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeDeleteModal" class="mr-3">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteSupplier"
                    >
                        Eliminar Proveedor
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { PlusIcon, PencilIcon, TrashIcon, MagnifyingGlassIcon, TruckIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    suppliers: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
})

const search = ref(props.filters.search)
const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const selectedSupplier = ref(null)

const form = useForm({
    name: '',
    contact_name: '',
    phone: '',
    email: '',
    address: '',
    is_active: true
})

// Watch for changes in search input
watch(search, (value) => {
    router.get(
        route('admin.suppliers.index'),
        { search: value },
        { preserveState: true, preserveScroll: true }
    )
})

const filteredSuppliers = computed(() => {
    return props.suppliers
})

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    form.clearErrors()
    showModal.value = true
}

const openEditModal = (supplier) => {
    isEditing.value = true
    selectedSupplier.value = supplier
    form.clearErrors()
    form.name = supplier.name
    form.contact_name = supplier.contact_name
    form.phone = supplier.phone
    form.email = supplier.email
    form.address = supplier.address
    form.is_active = supplier.is_active
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.suppliers.update', selectedSupplier.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.suppliers.store'), {
            onSuccess: () => closeModal(),
        })
    }
}

const openDeleteModal = (supplier) => {
    selectedSupplier.value = supplier
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedSupplier.value = null
}

const deleteSupplier = () => {
    form.delete(route('admin.suppliers.destroy', selectedSupplier.value.id), {
        onSuccess: () => closeDeleteModal(),
    })
}

const toggleSupplierStatus = (supplier) => {
    form.post(route('admin.suppliers.toggle-status', supplier.id))
}
</script> 