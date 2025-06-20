<template>
    <Head title="Nueva Venta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Nueva Venta
                </h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <ShoppingCartIcon class="h-5 w-5" />
                    <span>Punto de Venta</span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Alertas -->
                <div v-if="flash.success" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <CheckCircleIcon class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ flash.success }}</p>
                        </div>
                    </div>
                </div>

                <div v-if="flash.error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ flash.error }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Panel de Productos -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                            <CubeIcon class="h-5 w-5 text-blue-600" />
                                        </div>
                                        Seleccionar Productos
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-500">{{ filteredProducts.length }} productos</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros -->
                            <div class="p-6 border-b border-gray-100 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                                        <div class="relative">
                                            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                            <input
                                                v-model="filters.search"
                                                type="text"
                                                placeholder="Nombre, SKU o descripción..."
                                                class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                                        <select v-model="filters.category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Todas las categorías</option>
                                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                                        <select v-model="filters.brand" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Todas las marcas</option>
                                            <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                                                {{ brand.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                        <select v-model="filters.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Todos los estados</option>
                                            <option value="active">Activo</option>
                                            <option value="inactive">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Productos -->
                            <div class="p-6">
                                <div v-if="filteredProducts.length === 0" class="text-center py-12">
                                    <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                        <CubeIcon class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <p class="text-gray-500 font-medium">No se encontraron productos</p>
                                    <p class="text-sm text-gray-400 mt-1">Intenta ajustar los filtros de búsqueda</p>
                                </div>

                                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                                    <div
                                        v-for="product in filteredProducts"
                                        :key="product.id"
                                        class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer"
                                        @click="addToCart(product)"
                                    >
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <img
                                                    v-if="product.image"
                                                    :src="product.image"
                                                    :alt="product.name"
                                                    class="h-12 w-12 rounded-lg object-cover"
                                                />
                                                <div v-else class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <CubeIcon class="h-6 w-6 text-gray-400" />
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                                                <p class="text-xs text-gray-500">{{ product.sku }}</p>
                                                <div class="flex items-center justify-between mt-1">
                                                    <span class="text-sm font-semibold text-blue-600">S/ {{ product.price }}</span>
                                                    <span class="text-xs" :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">
                                                        Stock: {{ product.stock }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Carrito -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden sticky top-6">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                                        <ShoppingCartIcon class="h-5 w-5 text-green-600" />
                                    </div>
                                    Carrito de Compras
                                </h3>
                            </div>

                            <div class="p-6">
                                <!-- Items del Carrito -->
                                <div v-if="cart.length === 0" class="text-center py-8">
                                    <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                        <ShoppingCartIcon class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <p class="text-gray-500 font-medium">Carrito vacío</p>
                                    <p class="text-sm text-gray-400 mt-1">Selecciona productos para agregar</p>
                                </div>

                                <div v-else class="space-y-4 max-h-64 overflow-y-auto mb-6">
                                    <div
                                        v-for="item in cart"
                                        :key="item.id"
                                        class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ item.name }}</p>
                                            <p class="text-xs text-gray-500">S/ {{ item.price }} c/u</p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button
                                                @click="decreaseQuantity(item.id)"
                                                class="w-6 h-6 rounded-full bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center text-xs font-bold"
                                            >
                                                -
                                            </button>
                                            <span class="text-sm font-medium w-8 text-center">{{ item.quantity }}</span>
                                            <button
                                                @click="increaseQuantity(item.id)"
                                                class="w-6 h-6 rounded-full bg-green-100 text-green-600 hover:bg-green-200 flex items-center justify-center text-xs font-bold"
                                            >
                                                +
                                            </button>
                                        </div>
                                        <button
                                            @click="removeFromCart(item.id)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            <TrashIcon class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Resumen -->
                                <div v-if="cart.length > 0" class="border-t border-gray-200 pt-4">
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span class="font-medium">S/ {{ subtotal.toFixed(2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">IGV (18%):</span>
                                            <span class="font-medium">S/ {{ tax.toFixed(2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                                            <span>Total:</span>
                                            <span class="text-green-600">S/ {{ total.toFixed(2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Información del Cliente -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                                        <input
                                            v-model="form.customer_name"
                                            type="text"
                                            placeholder="Nombre del cliente (opcional)"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                        />
                                    </div>

                                    <!-- Botones de Acción -->
                                    <div class="space-y-3">
                                        <button
                                            @click="processSale"
                                            :disabled="processing || cart.length === 0"
                                            class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                                        >
                                            <span v-if="processing" class="flex items-center justify-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Procesando...
                                            </span>
                                            <span v-else class="flex items-center justify-center">
                                                <CurrencyDollarIcon class="h-5 w-5 mr-2" />
                                                Procesar Venta
                                            </span>
                                        </button>
                                        <button
                                            @click="clearCart"
                                            class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
                                        >
                                            Limpiar Carrito
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import {
    ShoppingCartIcon,
    CubeIcon,
    MagnifyingGlassIcon,
    TrashIcon,
    CurrencyDollarIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const { flash } = usePage().props;

const props = defineProps({
    products: {
        type: Array,
        required: true,
        default: () => []
    },
    categories: {
        type: Array,
        required: true,
        default: () => []
    },
    brands: {
        type: Array,
        required: true,
        default: () => []
    }
});

// Estado reactivo
const cart = ref([]);
const processing = ref(false);
const filters = ref({
    search: '',
    category: '',
    brand: '',
    status: ''
});

// Formulario
const form = useForm({
    customer_name: '',
    items: [],
    total: 0
});

// Computed properties
const filteredProducts = computed(() => {
    let filtered = props.products;

    if (filters.value.search) {
        const search = filters.value.search.toLowerCase();
        filtered = filtered.filter(product =>
            product.name.toLowerCase().includes(search) ||
            product.sku.toLowerCase().includes(search) ||
            (product.description && product.description.toLowerCase().includes(search))
        );
    }

    if (filters.value.category) {
        filtered = filtered.filter(product => product.category_id == filters.value.category);
    }

    if (filters.value.brand) {
        filtered = filtered.filter(product => product.brand_id == filters.value.brand);
    }

    if (filters.value.status) {
        filtered = filtered.filter(product => product.status === filters.value.status);
    }

    return filtered.filter(product => product.stock > 0);
});

const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const tax = computed(() => {
    return subtotal.value * 0.18;
});

const total = computed(() => {
    return subtotal.value + tax.value;
});

// Métodos
const addToCart = (product) => {
    if (product.stock <= 0) {
        alert('Producto sin stock disponible');
        return;
    }

    const existingItem = cart.value.find(item => item.id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
        } else {
            alert('No hay suficiente stock disponible');
        }
    } else {
        cart.value.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            stock: product.stock
        });
    }
};

const removeFromCart = (productId) => {
    const index = cart.value.findIndex(item => item.id === productId);
    if (index > -1) {
        cart.value.splice(index, 1);
    }
};

const increaseQuantity = (productId) => {
    const item = cart.value.find(item => item.id === productId);
    if (item && item.quantity < item.stock) {
        item.quantity++;
    } else {
        alert('No hay suficiente stock disponible');
    }
};

const decreaseQuantity = (productId) => {
    const item = cart.value.find(item => item.id === productId);
    if (item) {
        if (item.quantity > 1) {
            item.quantity--;
        } else {
            removeFromCart(productId);
        }
    }
};

const clearCart = () => {
    if (confirm('¿Estás seguro de que quieres limpiar el carrito?')) {
        cart.value = [];
    }
};

const processSale = () => {
    if (cart.value.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    processing.value = true;
    
    form.items = cart.value.map(item => ({
        product_id: item.id,
        quantity: item.quantity,
        price: item.price
    }));
    
    form.total = total.value;

    form.post(route('employee.sales.store'), {
        onSuccess: () => {
            cart.value = [];
            form.reset();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};

onMounted(() => {
    console.log('Sales Create mounted with props:', props);
});
</script> 