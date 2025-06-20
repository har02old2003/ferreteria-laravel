<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <PageHeader
            title="Productos"
            description="Gestiona el inventario de productos de la ferretería"
        >
            <template #actions>
                <ActionButton
                    text="Nuevo Producto"
                    :icon-left="PlusIcon"
                    @click="openCreateModal"
                />
            </template>
        </PageHeader>

        <DataTable
            :columns="columns"
            :items="products.data"
            :pagination="products"
            search-placeholder="Buscar productos..."
            @update:search="handleSearch"
        >
            <template #price="{ item }">
                ${{ typeof item.price === 'number' ? item.price.toFixed(2) : '0.00' }}
            </template>

            <template #stock="{ item }">
                <span
                    :class="[
                        'px-2 py-1 text-xs font-medium rounded-full',
                        item.stock > item.stock_min ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                    ]"
                >
                    {{ item.stock }}
                </span>
            </template>

            <template #actions="{ item }">
                <div class="flex items-center space-x-3">
                    <ActionButton
                        variant="secondary"
                        size="sm"
                        :icon-left="PencilIcon"
                        @click="openEditModal(item)"
                    />
                    <ActionButton
                        variant="danger"
                        size="sm"
                        :icon-left="TrashIcon"
                        @click="confirmDelete(item)"
                    />
                </div>
            </template>
        </DataTable>

        <!-- Modal de Crear/Editar -->
        <Modal
            :show="showModal"
            :title="isEditing ? 'Editar Producto' : 'Nuevo Producto'"
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
                        <InputLabel for="description" value="Descripción" />
                        <TextArea
                            id="description"
                            v-model="form.description"
                            class="mt-1 block w-full"
                            rows="3"
                        />
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="price" value="Precio" />
                            <TextInput
                                id="price"
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.price" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="stock" value="Stock" />
                            <TextInput
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.stock" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <InputLabel for="brand_id" value="Marca" />
                            <SelectInput
                                id="brand_id"
                                v-model="form.brand_id"
                                :options="brands"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.brand_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="category_id" value="Categoría" />
                            <SelectInput
                                id="category_id"
                                v-model="form.category_id"
                                :options="categories"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.category_id" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="supplier_id" value="Proveedor" />
                        <SelectInput
                            id="supplier_id"
                            v-model="form.supplier_id"
                            :options="suppliers"
                            class="mt-1 block w-full"
                        />
                        <InputError :message="form.errors.supplier_id" class="mt-2" />
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
                    ¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.
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
                        @click="deleteProduct"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/Common/PageHeader.vue'
import DataTable from '@/Components/Common/DataTable.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    products: {
        type: Object,
        required: true
    },
    brands: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    suppliers: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
})

const columns = [
    { key: 'name', label: 'Nombre' },
    { key: 'brand', label: 'Marca' },
    { key: 'category', label: 'Categoría' },
    { key: 'price', label: 'Precio' },
    { key: 'stock', label: 'Stock' }
]

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditing = ref(false)
const selectedProduct = ref(null)

const form = useForm({
    name: '',
    description: '',
    price: '',
    stock: '',
    brand_id: '',
    category_id: '',
    supplier_id: ''
})

const deleteForm = useForm({})

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    showModal.value = true
}

const openEditModal = (product) => {
    isEditing.value = true
    selectedProduct.value = product
    form.name = product.name
    form.description = product.description
    form.price = product.price
    form.stock = product.stock
    form.brand_id = product.brand_id
    form.category_id = product.category_id
    form.supplier_id = product.supplier_id || ''
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset({
        supplier_id: ''
    })
    form.clearErrors()
}

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('admin.products.update', selectedProduct.value.id), {
            onSuccess: () => closeModal()
        })
    } else {
        form.post(route('admin.products.store'), {
            onSuccess: () => closeModal()
        })
    }
}

const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedProduct.value = null
}

const deleteProduct = () => {
    deleteForm.delete(route('admin.products.destroy', selectedProduct.value.id), {
        onSuccess: () => closeDeleteModal()
    })
}

const handleSearch = (query) => {
    router.get(
        route('admin.products.index'),
        { search: query },
        { preserveState: true, preserveScroll: true }
    )
}
</script> 