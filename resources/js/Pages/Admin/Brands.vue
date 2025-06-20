<template>
    <Head title="Marcas" />

    <AuthenticatedLayout>
        <div class="py-6">
            <PageHeader
                title="Marcas"
                description="Gestiona las marcas de productos"
            >
                <template #actions>
                    <ActionButton
                        text="Nueva Marca"
                        :icon-left="PlusIcon"
                        @click="openCreateModal"
                    />
                </template>
            </PageHeader>

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
                <DataTable
                    :columns="columns"
                    :items="brands.data"
                    :pagination="brands"
                    search-placeholder="Buscar marcas..."
                    empty-title="No hay marcas"
                    empty-message="Comienza creando una nueva marca"
                    @update:search="handleSearch"
                >
                    <template #status="{ item }">
                        <span
                            :class="[
                                'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                item.is_active 
                                    ? 'bg-green-50 text-green-700 ring-green-600/20'
                                    : 'bg-red-50 text-red-700 ring-red-600/20'
                            ]"
                        >
                            <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                                item.is_active ? 'bg-green-600' : 'bg-red-600'
                            ]" />
                            {{ item.is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </template>

                    <template #actions="{ item }">
                        <div class="flex items-center gap-2">
                            <ActionButton
                                variant="secondary"
                                size="sm"
                                :icon-left="PencilIcon"
                                icon-only
                                :title="'Editar ' + item.name"
                                @click="openEditModal(item)"
                            >
                                <span class="sr-only">Editar</span>
                            </ActionButton>
                            <ActionButton
                                variant="danger"
                                size="sm"
                                :icon-left="TrashIcon"
                                icon-only
                                :title="'Eliminar ' + item.name"
                                @click="confirmDelete(item)"
                            >
                                <span class="sr-only">Eliminar</span>
                            </ActionButton>
                        </div>
                    </template>

                    <template #empty>
                        <ActionButton
                            variant="primary"
                            :icon-left="PlusIcon"
                            text="Nueva Marca"
                            @click="openCreateModal"
                        />
                    </template>
                </DataTable>
            </div>
        </div>

        <!-- Modal de Crear/Editar -->
        <Modal
            :show="showModal"
            :title="isEditing ? 'Editar Marca' : 'Nueva Marca'"
            @close="closeModal"
            :max-width="'xl'"
        >
            <form @submit.prevent="submitForm" class="space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="name" value="Nombre" required />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            placeholder="Ingresa el nombre de la marca"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Descripción" />
                        <TextArea
                            id="description"
                            v-model="form.description"
                            class="mt-1 block w-full"
                            rows="3"
                            placeholder="Describe la marca brevemente"
                        />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-2">
                        <Switch
                            v-model="form.is_active"
                            :class="[form.is_active ? 'bg-indigo-600' : 'bg-gray-200']"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                        >
                            <span
                                :class="[form.is_active ? 'translate-x-5' : 'translate-x-0']"
                                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            >
                                <span
                                    :class="[form.is_active ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in']"
                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                >
                                    <XMarkIcon class="h-3 w-3 text-gray-400" />
                                </span>
                                <span
                                    :class="[form.is_active ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out']"
                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                >
                                    <CheckIcon class="h-3 w-3 text-indigo-600" />
                                </span>
                            </span>
                        </Switch>
                        <InputLabel for="is_active" value="Marca activa" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
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
            :max-width="'lg'"
        >
            <div class="p-6">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                        ¿Eliminar marca?
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro de que deseas eliminar la marca "{{ selectedBrand?.name }}"? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
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
                        @click="deleteBrand"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    ExclamationTriangleIcon,
    CheckIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/Common/PageHeader.vue'
import DataTable from '@/Components/Common/DataTable.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import InputError from '@/Components/InputError.vue'
import Switch from '@/Components/Switch.vue'

const props = defineProps({
    brands: {
        type: Object,
        required: true
    }
})

const columns = [
    { key: 'name', label: 'Nombre' },
    { key: 'description', label: 'Descripción' },
    { key: 'status', label: 'Estado' }
]

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const selectedBrand = ref(null)

const form = useForm({
    name: '',
    description: '',
    is_active: true
})

const deleteForm = useForm({})

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    showModal.value = true
}

const openEditModal = (brand) => {
    isEditing.value = true
    selectedBrand.value = brand
    form.name = brand.name
    form.description = brand.description
    form.is_active = brand.is_active
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.brands.update', selectedBrand.value.id), {
            onSuccess: () => closeModal()
        })
    } else {
        form.post(route('admin.brands.store'), {
            onSuccess: () => closeModal()
        })
    }
}

const confirmDelete = (brand) => {
    selectedBrand.value = brand
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedBrand.value = null
}

const deleteBrand = () => {
    deleteForm.delete(route('admin.brands.destroy', selectedBrand.value.id), {
        onSuccess: () => closeDeleteModal()
    })
}

const handleSearch = (query) => {
    router.get(
        route('admin.brands.index'),
        { search: query },
        { preserveState: true, preserveScroll: true }
    )
}
</script> 