<template>
    <form @submit.prevent="form.id ? update() : store()" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Nombre -->
            <div>
                <InputLabel for="name" value="Nombre del Producto" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Categoría -->
            <div>
                <InputLabel for="category_id" value="Categoría" />
                <select
                    id="category_id"
                    v-model="form.category_id"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required
                >
                    <option value="">Seleccione una categoría</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <InputError :message="form.errors.category_id" class="mt-2" />
            </div>

            <!-- Marca -->
            <div>
                <InputLabel for="brand_id" value="Marca" />
                <select
                    id="brand_id"
                    v-model="form.brand_id"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option value="">Seleccione una marca</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                        {{ brand.name }}
                    </option>
                </select>
                <InputError :message="form.errors.brand_id" class="mt-2" />
            </div>

            <!-- Proveedor -->
            <div>
                <InputLabel for="supplier_id" value="Proveedor" />
                <select
                    id="supplier_id"
                    v-model="form.supplier_id"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option value="">Seleccione un proveedor</option>
                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                        {{ supplier.name }}
                    </option>
                </select>
                <InputError :message="form.errors.supplier_id" class="mt-2" />
            </div>

            <!-- Unidad -->
            <div>
                <InputLabel for="unit" value="Unidad" />
                <select
                    id="unit"
                    v-model="form.unit"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    required
                >
                    <option value="">Seleccione una unidad</option>
                    <option value="unidad">Unidad</option>
                    <option value="kg">Kilogramo</option>
                    <option value="litro">Litro</option>
                    <option value="metro">Metro</option>
                    <option value="lote">Lote</option>
                    <option value="caja">Caja</option>
                    <option value="paquete">Paquete</option>
                </select>
                <InputError :message="form.errors.unit" class="mt-2" />
            </div>

            <!-- Precio -->
            <div>
                <InputLabel for="price" value="Precio" />
                <TextInput
                    id="price"
                    type="number"
                    step="0.01"
                    class="mt-1 block w-full"
                    v-model="form.price"
                    required
                />
                <InputError :message="form.errors.price" class="mt-2" />
            </div>

            <!-- Stock -->
            <div>
                <InputLabel for="stock" value="Stock" />
                <TextInput
                    id="stock"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.stock"
                    required
                />
                <InputError :message="form.errors.stock" class="mt-2" />
            </div>
        </div>

        <!-- Descripción -->
        <div>
            <InputLabel for="description" value="Descripción" />
            <textarea
                id="description"
                v-model="form.description"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                rows="3"
                required
            ></textarea>
            <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <div class="flex justify-end gap-2">
            <PrimaryButton type="submit" :disabled="form.processing">
                {{ form.id ? 'Actualizar' : 'Crear' }}
            </PrimaryButton>
            <SecondaryButton
                type="button"
                @click="$emit('cancel')"
            >
                Cancelar
            </SecondaryButton>
        </div>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    product: {
        type: Object,
        default: () => ({})
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
});

const emit = defineEmits(['cancel']);

const form = useForm({
    id: props.product?.id,
    name: props.product?.name || '',
    description: props.product?.description || '',
    price: props.product?.price || '',
    stock: props.product?.stock || '',
    category_id: props.product?.category_id || '',
    brand_id: props.product?.brand_id || '',
    supplier_id: props.product?.supplier_id || '',
    unit: props.product?.unit || ''
});

const store = () => {
    form.post(route('admin.products.store'), {
        onSuccess: () => {
            form.reset();
            emit('cancel');
        },
    });
};

const update = () => {
    form.put(route('admin.products.update', form.id), {
        onSuccess: () => emit('cancel'),
    });
};
</script> 