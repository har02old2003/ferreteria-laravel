<template>
    <Head title="Consulta de Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    Productos
                </h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <CubeIcon class="h-5 w-5" />
                    <span>Solo lectura - Empleado</span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Productos</p>
                                <p class="text-2xl font-bold">{{ safeProducts.total }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CubeIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Productos Activos</p>
                                <p class="text-2xl font-bold">{{ activeProductsCount }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CheckCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-yellow-100 text-sm font-medium">Stock Bajo</p>
                                <p class="text-2xl font-bold">{{ lowStockCount }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ExclamationTriangleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Disponibles</p>
                                <p class="text-2xl font-bold">{{ availableProductsCount }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CheckCircleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel de Productos -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <CubeIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Catálogo de Productos
                            </h3>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span>{{ safeProducts.data.length }} de {{ safeProducts.total }} productos</span>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros de búsqueda -->
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Búsqueda -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                                <div class="relative">
                                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
                                    <input
                                        v-model="searchForm.search"
                                        type="text"
                                        placeholder="Nombre, SKU..."
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        @input="search"
                                    />
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                                <select
                                    v-model="searchForm.category_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="search"
                                >
                                    <option value="">Todas las categorías</option>
                                    <option v-for="category in safeCategories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Marca -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                                <select
                                    v-model="searchForm.brand_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="search"
                                >
                                    <option value="">Todas las marcas</option>
                                    <option v-for="brand in safeBrands" :key="brand.id" :value="brand.id">
                                        {{ brand.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Proveedor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                                <select
                                    v-model="searchForm.supplier_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    @change="search"
                                >
                                    <option value="">Todos los proveedores</option>
                                    <option v-for="supplier in safeSuppliers" :key="supplier.id" :value="supplier.id">
                                        {{ supplier.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de productos -->
                    <div class="p-6">
                        <div v-if="safeProducts.data.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <CubeIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No se encontraron productos</p>
                            <p class="text-sm text-gray-400 mt-1">Intenta ajustar los filtros de búsqueda</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                        <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
                                        <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoría</th>
                                        <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Marca</th>
                                        <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="product in safeProducts.data" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ product.name || 'Sin nombre' }}</div>
                                                <div class="text-sm text-gray-500">SKU: {{ product.sku || 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <span class="text-lg font-semibold text-green-600">S/ {{ formatPrice(product.price) }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span :class="{
                                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                                'bg-red-100 text-red-800': product.low_stock,
                                                'bg-green-100 text-green-800': !product.low_stock
                                            }">
                                                {{ product.stock || 0 }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-sm text-gray-900">{{ product.category || 'Sin categoría' }}</span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="text-sm text-gray-900">{{ product.brand || 'Sin marca' }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span :class="{
                                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                                'bg-green-100 text-green-800': product.status === 'active',
                                                'bg-red-100 text-red-800': product.status === 'inactive'
                                            }">
                                                {{ product.status === 'active' ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="safeProducts.links && safeProducts.data.length > 0" class="mt-6 border-t border-gray-200 pt-6">
                            <nav class="flex items-center justify-between">
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Mostrando <span class="font-medium">{{ safeProducts.from || 0 }}</span> 
                                            a <span class="font-medium">{{ safeProducts.to || 0 }}</span> 
                                            de <span class="font-medium">{{ safeProducts.total || 0 }}</span> resultados
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <Link v-for="link in safeProducts.links" :key="link.label" 
                                                  :href="link.url" 
                                                  v-html="link.label" 
                                                  :class="{
                                                      'bg-blue-50 border-blue-500 text-blue-600': link.active,
                                                      'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active,
                                                      'cursor-not-allowed opacity-50': !link.url
                                                  }" 
                                                  class="relative inline-flex items-center px-3 py-2 border text-sm font-medium">
                                            </Link>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    CubeIcon,
    MagnifyingGlassIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], total: 0, links: [] })
    },
    categories: {
        type: Array,
        default: () => []
    },
    brands: {
        type: Array,
        default: () => []
    },
    suppliers: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Estado reactivo
const searchForm = ref({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    brand_id: props.filters?.brand_id || '',
    supplier_id: props.filters?.supplier_id || ''
});

// Computed properties seguras
const safeProducts = computed(() => {
    return {
        data: props.products?.data || [],
        total: props.products?.total || 0,
        links: props.products?.links || [],
        from: props.products?.from || 0,
        to: props.products?.to || 0
    };
});

const safeCategories = computed(() => props.categories || []);
const safeBrands = computed(() => props.brands || []);
const safeSuppliers = computed(() => props.suppliers || []);

const activeProductsCount = computed(() => {
    return safeProducts.value.data.filter(product => product.status === 'active').length;
});

const lowStockCount = computed(() => {
    return safeProducts.value.data.filter(product => product.low_stock === true).length;
});

const availableProductsCount = computed(() => {
    return safeProducts.value.data.filter(product => (product.stock || 0) > 0).length;
});

// Métodos
const formatPrice = (price) => {
    if (!price) return '0.00';
    return typeof price === 'number' ? price.toFixed(2) : parseFloat(price).toFixed(2);
};

const search = () => {
    router.get(route('employee.products.index'), searchForm.value, {
        preserveState: true,
        replace: true
    });
};
</script> 