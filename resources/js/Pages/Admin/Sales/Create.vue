<template>
    <Head title="Nueva Venta" />

    <AuthenticatedLayout>
        <PageHeader
            title="Nueva Venta"
            description="Registra una nueva venta"
        >
            <template #actions>
                <ActionButton
                    variant="secondary"
                    text="Volver"
                    :icon-left="ArrowLeftIcon"
                    @click="router.get(route('admin.sales.index'))"
                />
            </template>
        </PageHeader>

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <form @submit.prevent="submitForm" class="space-y-6">
                <!-- Selección de Categoría, Marca y Proveedor -->
                <div class="bg-white shadow rounded-lg p-6 space-y-6">
                    <div>
                        <InputLabel for="category" value="Categoría" />
                        <SelectInput
                            id="category"
                            v-model="form.category_id"
                            :options="categories"
                            option-value="id"
                            option-label="name"
                            placeholder="Selecciona una categoría"
                        />
                        <InputError :message="form.errors.category_id" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel for="brand" value="Marca" />
                        <SelectInput
                            id="brand"
                            v-model="form.brand_id"
                            :options="brands"
                            option-value="id"
                            option-label="name"
                            placeholder="Selecciona una marca"
                        />
                        <InputError :message="form.errors.brand_id" class="mt-1" />
                    </div>
                    <div>
                        <InputLabel for="supplier" value="Proveedor" />
                        <SelectInput
                            id="supplier"
                            v-model="form.supplier_id"
                            :options="suppliers"
                            option-value="id"
                            option-label="name"
                            placeholder="Selecciona un proveedor"
                        />
                        <InputError :message="form.errors.supplier_id" class="mt-1" />
                    </div>
                </div>

                <!-- Selección de Productos -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
                    
                    <div class="space-y-4">
                        <div v-for="(item, index) in form.products" :key="index" class="flex items-center gap-4">
                            <div class="flex-1">
                                <SelectInput
                                    v-model="item.id"
                                    :options="availableProducts"
                                    option-value="id"
                                    option-label="name"
                                    placeholder="Selecciona un producto"
                                    @update:model-value="updateProduct(index)"
                                />
                                <InputError :message="form.errors[`products.${index}.id`]" class="mt-1" />
                            </div>
                            
                            <div class="w-32">
                                <TextInput
                                    v-model="item.quantity"
                                    type="number"
                                    min="1"
                                    :max="getMaxStock(item.id)"
                                    placeholder="Cantidad"
                                />
                                <InputError :message="form.errors[`products.${index}.quantity`]" class="mt-1" />
                            </div>

                            <div class="w-32 text-right">
                                <p class="text-sm text-gray-600">Subtotal:</p>
                                <p class="font-medium">S/. {{ calculateSubtotal(item) }}</p>
                            </div>

                            <button
                                type="button"
                                class="text-red-600 hover:text-red-800"
                                @click="removeProduct(index)"
                            >
                                <TrashIcon class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="pt-4">
                            <ActionButton
                                type="button"
                                variant="secondary"
                                text="Agregar Producto"
                                :icon-left="PlusIcon"
                                @click="addProduct"
                            />
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Total</h3>
                        <p class="text-2xl font-bold">S/. {{ calculateTotal() }}</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-4">
                    <ActionButton
                        variant="secondary"
                        text="Cancelar"
                        @click="router.get(route('admin.sales.index'))"
                    />
                    <ActionButton
                        type="submit"
                        text="Registrar Venta"
                        :loading="form.processing"
                        :disabled="form.processing || !form.products.length"
                    />
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { PlusIcon, TrashIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/Common/PageHeader.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import SelectInput from '@/Components/SelectInput.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    brands: {
        type: Array,
        required: true
    },
    suppliers: {
        type: Array,
        required: true
    }
})

const form = useForm({
    category_id: '',
    brand_id: '',
    supplier_id: '',
    products: []
})

const availableProducts = computed(() => {
    return props.products.filter(product => {
        // Excluir productos que ya están en el formulario
        const isSelected = form.products.some(item => item.id === product.id)
        return !isSelected
    })
})

const addProduct = () => {
    form.products.push({
        id: '',
        quantity: 1
    })
}

const removeProduct = (index) => {
    form.products.splice(index, 1)
}

const updateProduct = (index) => {
    const product = form.products[index]
    if (product.id) {
        const selectedProduct = props.products.find(p => p.id === product.id)
        if (selectedProduct && product.quantity > selectedProduct.stock) {
            product.quantity = selectedProduct.stock
        }
    }
}

const getMaxStock = (productId) => {
    if (!productId) return 1
    const product = props.products.find(p => p.id === productId)
    return product ? product.stock : 1
}

const calculateSubtotal = (item) => {
    if (!item.id || !item.quantity) return '0.00'
    const product = props.products.find(p => p.id === item.id)
    if (!product) return '0.00'
    return (product.price * item.quantity).toFixed(2)
}

const calculateTotal = () => {
    return form.products.reduce((total, item) => {
        return total + parseFloat(calculateSubtotal(item))
    }, 0).toFixed(2)
}

const submitForm = () => {
    form.post(route('admin.sales.store'), {
        onSuccess: () => {
            router.get(route('admin.sales.index'))
        }
    })
}
</script>
