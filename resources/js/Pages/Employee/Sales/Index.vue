<template>
    <Head title="Ventas" />

    <AuthenticatedLayout>
        <PageHeader
            title="Ventas"
            description="Gestión de ventas de la ferretería"
        />

        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Mensaje de Error -->
            <div v-if="error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <ExclamationTriangleIcon class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ error }}</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                            <p class="text-2xl font-bold">{{ safeSales.length }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            <ShoppingCartIcon class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Ventas Completadas</p>
                            <p class="text-2xl font-bold">{{ completedSales }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            <CheckCircleIcon class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Ingresos</p>
                            <p class="text-2xl font-bold">S/ {{ formatCurrency(totalRevenue) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            <CurrencyDollarIcon class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Promedio por Venta</p>
                            <p class="text-2xl font-bold">S/ {{ formatCurrency(averageSale) }}</p>
                        </div>
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            <ChartBarIcon class="h-8 w-8" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón Nueva Venta -->
            <div class="mb-6 flex justify-end">
                <ActionButton
                    text="Nueva Venta"
                    :icon-left="PlusIcon"
                    @click="openCreateModal"
                />
            </div>

            <DataTable
                :columns="columns"
                :items="safeSales"
                search-placeholder="Buscar ventas..."
                @update:search="handleSearch"
            >
                <template #code="{ item }">
                    {{ item.code || 'SIN-CODIGO' }}
                </template>

                <template #created_at="{ item }">
                    {{ formatDate(item.created_at) }}
                </template>

                <template #customer_name="{ item }">
                    {{ item.customer_name || 'Cliente General' }}
                </template>

                <template #total="{ item }">
                    S/ {{ formatTotal(item.total) }}
                </template>

                <template #status="{ item }">
                    <span
                        :class="[
                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                            {
                                'bg-green-50 text-green-700 ring-green-600/20': item.status === 'completed',
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20': item.status === 'pending',
                                'bg-red-50 text-red-700 ring-red-600/20': item.status === 'cancelled'
                            }
                        ]"
                    >
                        <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                            item.status === 'completed' ? 'bg-green-600' : 
                            item.status === 'pending' ? 'bg-yellow-600' : 'bg-red-600'
                        ]" />
                        {{ formatStatus(item.status) }}
                    </span>
                </template>

                <template #actions="{ item }">
                    <div class="flex items-center gap-2">
                        <ActionButton
                            variant="secondary"
                            size="sm"
                            :icon-left="EyeIcon"
                            icon-only
                            :title="'Ver detalles de ' + item.code"
                            @click="viewSaleDetails(item)"
                        >
                            <span class="sr-only">Ver detalles</span>
                        </ActionButton>
                        <ActionButton
                            v-if="item.status === 'completed'"
                            variant="primary"
                            size="sm"
                            :icon-left="PrinterIcon"
                            text="Imprimir"
                            :title="'Imprimir ticket de ' + item.code"
                            @click="printTicket(item)"
                        />
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Modal de Nueva Venta -->
        <Modal
            :show="showCreateModal"
            title="Nueva Venta"
            @close="closeCreateModal"
            :max-width="'2xl'"
        >
            <form @submit.prevent="submitSale" class="p-4">
                <div class="space-y-4">
                    <!-- Filtros de productos -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <InputLabel for="category_filter" value="Filtrar por Categoría" />
                            <SelectInput
                                id="category_filter"
                                v-model="filters.category_id"
                                :options="categoryOptions"
                                class="mt-2 block w-full"
                                @change="filterProducts"
                            />
                        </div>
                        <div>
                            <InputLabel for="brand_filter" value="Filtrar por Marca" />
                            <SelectInput
                                id="brand_filter"
                                v-model="filters.brand_id"
                                :options="brandOptions"
                                class="mt-2 block w-full"
                                @change="filterProducts"
                            />
                        </div>
                        <div>
                            <InputLabel for="supplier_filter" value="Filtrar por Proveedor" />
                            <SelectInput
                                id="supplier_filter"
                                v-model="filters.supplier_id"
                                :options="supplierOptions"
                                class="mt-2 block w-full"
                                @change="filterProducts"
                            />
                        </div>
                    </div>

                    <!-- Información del Cliente -->
                    <div class="border-t border-gray-200 pt-4">
                        <InputLabel value="Información del Cliente (Opcional)" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="customer_dni" value="DNI del Cliente" />
                                <TextInput
                                    id="customer_dni"
                                    v-model="saleForm.customer_dni"
                                    type="text"
                                    placeholder="Ingrese el DNI (8 dígitos)"
                                    class="mt-1 block w-full"
                                    maxlength="8"
                                    @input="searchClientByDni"
                                />
                                <p v-if="searchingClient" class="mt-1 text-xs text-blue-600">
                                    🔍 Buscando cliente...
                                </p>
                                <p v-if="clientNotFound" class="mt-1 text-xs text-orange-600">
                                    ⚠️ Cliente no encontrado, se creará uno nuevo
                                </p>
                                <p v-if="clientFound" class="mt-1 text-xs text-green-600">
                                    ✅ Cliente encontrado
                                </p>
                            </div>
                            <div>
                                <InputLabel for="customer_name" value="Nombre del Cliente" />
                                <TextInput
                                    id="customer_name"
                                    v-model="saleForm.customer_name"
                                    type="text"
                                    placeholder="Ingrese el nombre del cliente"
                                    class="mt-1 block w-full"
                                    :readonly="clientFound"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Selección de productos -->
                    <div>
                        <InputLabel value="Seleccionar Productos" />
                        <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md">
                            <div class="grid grid-cols-1 divide-y divide-gray-200">
                                <div 
                                    v-for="product in filteredProducts" 
                                    :key="product.id"
                                    class="p-3 hover:bg-gray-50 cursor-pointer"
                                    @click="addProductToSale(product)"
                                >
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ product.name }}</h4>
                                            <p class="text-xs text-gray-500">{{ product.category || 'Sin categoría' }} - {{ product.brand || 'Sin marca' }}</p>
                                            <p class="text-xs text-gray-400">SKU: {{ product.sku || 'N/A' }} | Stock: {{ product.stock }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">S/ {{ formatPrice(product.price) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos seleccionados -->
                    <div v-if="saleForm.products.length > 0">
                        <InputLabel value="Productos en la Venta" />
                        <div class="mt-2 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cant.</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acc.</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(item, index) in saleForm.products" :key="item.id">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.name }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">S/ {{ formatPrice(item.price) }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <TextInput
                                                v-model.number="item.quantity"
                                                type="number"
                                                min="1"
                                                :max="item.stock"
                                                class="w-16 text-sm"
                                                @input="updateSubtotal(index)"
                                            />
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">S/ {{ formatPrice(item.price * item.quantity) }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            <ActionButton
                                                variant="danger"
                                                size="sm"
                                                :icon-left="TrashIcon"
                                                icon-only
                                                @click="removeProductFromSale(index)"
                                            />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Total:</span>
                            <span class="text-2xl font-bold text-gray-900">S/ {{ formatPrice(calculateTotal()) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <ActionButton
                        variant="secondary"
                        text="Cancelar"
                        @click="closeCreateModal"
                    />
                    <ActionButton
                        variant="primary"
                        text="Registrar Venta"
                        :loading="saleForm.processing"
                        :disabled="saleForm.processing || saleForm.products.length === 0"
                        type="submit"
                    />
                </div>
            </form>
        </Modal>

        <!-- Modal de Detalles de Venta -->
        <Modal
            :show="showDetailsModal"
            title="Detalles de la Venta"
            @close="closeDetailsModal"
            :max-width="'2xl'"
        >
            <div v-if="loadingSaleDetails" class="p-6 text-center">
                <div class="flex items-center justify-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-3 text-gray-500">Cargando detalles de la venta...</span>
                </div>
            </div>
            <div v-else-if="selectedSale" class="p-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Información de la Venta</h3>
                        <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale?.id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Código</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale?.code }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span :class="{
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                        'bg-green-100 text-green-800': selectedSale.status === 'completed',
                                        'bg-yellow-100 text-yellow-800': selectedSale.status === 'pending',
                                        'bg-red-100 text-red-800': selectedSale.status === 'cancelled',
                                        'bg-gray-100 text-gray-800': !selectedSale.status
                                    }">
                                        {{ formatStatus(selectedSale.status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale?.user?.name || 'No asignado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale.customer_name || 'Cliente General' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(selectedSale.created_at) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                        <div class="mt-2 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Producto</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cantidad</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Precio Unit.</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <tr v-for="item in selectedSale?.products" :key="item.id">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">{{ item.name }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.quantity }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">S/ {{ formatPrice(item.price) }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">S/ {{ formatPrice(item.subtotal) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row" colspan="3" class="pl-4 pr-3 pt-4 text-right text-sm font-semibold text-gray-900">Total</th>
                                                <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold text-gray-900">S/ {{ formatPrice(selectedSale.total) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <ActionButton
                        v-if="selectedSale.status === 'completed'"
                        variant="primary"
                        :icon-left="PrinterIcon"
                        text="Imprimir Ticket"
                        @click="printTicket(selectedSale)"
                    />
                    <ActionButton
                        variant="secondary"
                        text="Cerrar"
                        @click="closeDetailsModal"
                    />
                </div>
            </div>
            <div v-else class="p-6 text-center">
                <p class="text-gray-500">Cargando detalles de la venta...</p>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PageHeader from '@/Components/Common/PageHeader.vue';
import DataTable from '@/Components/Common/DataTable.vue';
import ActionButton from '@/Components/Common/ActionButton.vue';
import {
    ShoppingCartIcon,
    PlusIcon,
    CheckCircleIcon,
    CurrencyDollarIcon,
    ChartBarIcon,
    DocumentTextIcon,
    EyeIcon,
    PrinterIcon,
    ArrowDownTrayIcon,
    ExclamationTriangleIcon,
    CubeIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    sales: {
        type: Object,
        required: false,
        default: () => ({
            data: [],
            links: [],
            meta: {
                current_page: 1,
                last_page: 1,
                per_page: 15,
                total: 0
            }
        })
    },
    error: {
        type: String,
        required: false
    }
});

// Estados de los modales
const showCreateModal = ref(false);
const showDetailsModal = ref(false);
const selectedSale = ref(null);
const loadingSaleDetails = ref(false);

// Datos para el modal de nueva venta
const allProducts = ref([]);
const categories = ref([]);
const brands = ref([]);
const suppliers = ref([]);

const filters = ref({
    category_id: '',
    brand_id: '',
    supplier_id: ''
});

const saleForm = useForm({
    products: [],
    customer_name: '',
    customer_dni: '',
    total: 0
});

// Variables para búsqueda de clientes
const searchingClient = ref(false);
const clientFound = ref(false);
const clientNotFound = ref(false);
const searchTimeout = ref(null);

// Definición de columnas para DataTable
const columns = [
    { key: 'code', label: 'Código' },
    { key: 'created_at', label: 'Fecha' },
    { key: 'customer_name', label: 'Cliente' },
    { key: 'total', label: 'Total' },
    { key: 'status', label: 'Estado' },
    { key: 'actions', label: 'Acciones' }
];

// Función de búsqueda
const handleSearch = (query) => {
    router.get(
        route('employee.sales.index'),
        { search: query },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};

// Computed properties seguras
const safeSales = computed(() => {
    if (!props.sales || !Array.isArray(props.sales.data)) {
        return [];
    }
    return props.sales.data;
});

const completedSales = computed(() => {
    return safeSales.value.filter(sale => sale.status === 'completed').length;
});

const totalRevenue = computed(() => {
    return safeSales.value
        .filter(sale => sale.status === 'completed')
        .reduce((sum, sale) => {
            const total = typeof sale.total === 'string' 
                ? parseFloat(sale.total.replace(/[S/\s,]/g, '')) 
                : (sale.total || 0);
            return sum + total;
        }, 0);
});

const averageSale = computed(() => {
    const completed = safeSales.value.filter(sale => sale.status === 'completed');
    return completed.length > 0 ? totalRevenue.value / completed.length : 0;
});

// Computed properties para las opciones de los filtros
const categoryOptions = computed(() => [
    { id: '', name: 'Todas las categorías' },
    ...categories.value.map(cat => ({ id: cat.id, name: cat.name }))
]);

const brandOptions = computed(() => [
    { id: '', name: 'Todas las marcas' },
    ...brands.value.map(brand => ({ id: brand.id, name: brand.name }))
]);

const supplierOptions = computed(() => [
    { id: '', name: 'Todos los proveedores' },
    ...suppliers.value.map(supplier => ({ id: supplier.id, name: supplier.name }))
]);

const filteredProducts = computed(() => {
    let filtered = allProducts.value;

    if (filters.value.category_id) {
        filtered = filtered.filter(p => p.category_id === filters.value.category_id);
    }
    if (filters.value.brand_id) {
        filtered = filtered.filter(p => p.brand_id === filters.value.brand_id);
    }
    if (filters.value.supplier_id) {
        filtered = filtered.filter(p => p.supplier_id === filters.value.supplier_id);
    }

    // Excluir productos ya agregados a la venta
    const addedProductIds = saleForm.products.map(p => p.id);
    filtered = filtered.filter(p => !addedProductIds.includes(p.id));

    return filtered;
});

// Métodos de formateo
const formatDate = (date) => {
    if (!date) return 'Fecha no disponible';
    try {
        return new Date(date).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return 'Fecha inválida';
    }
};

const formatStatus = (status) => {
    const statusMap = {
        'completed': 'Completada',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada'
    };
    return statusMap[status] || status || 'Desconocido';
};

const formatTotal = (total) => {
    if (total === null || total === undefined) return '0.00';
    
    if (typeof total === 'number') {
        return total.toFixed(2);
    }
    
    if (typeof total === 'string') {
        const cleaned = total.replace(/[S/\s,]/g, '');
        const num = parseFloat(cleaned);
        return isNaN(num) ? '0.00' : num.toFixed(2);
    }
    
    return '0.00';
};

const formatPrice = (price) => {
    if (price === null || price === undefined) return '0.00';
    
    if (typeof price === 'number') {
        return price.toFixed(2);
    }
    
    if (typeof price === 'string') {
        const cleaned = price.replace(/[S/\s,]/g, '');
        const num = parseFloat(cleaned);
        return isNaN(num) ? '0.00' : num.toFixed(2);
    }
    
    return '0.00';
};

const formatCurrency = (value) => {
    if (value === null || value === undefined) return '0.00';
    
    let numValue;
    
    if (typeof value === 'number') {
        numValue = value;
    } else if (typeof value === 'string') {
        const cleaned = value.replace(/[S/\s,]/g, '');
        numValue = parseFloat(cleaned);
        if (isNaN(numValue)) return '0.00';
    } else {
        return '0.00';
    }
    
    // Formatear con comas para miles y punto para decimales (formato US)
    return numValue.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

// Métodos de los modales
const openCreateModal = async () => {
    await loadSaleData();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    saleForm.reset();
    filters.value = {
        category_id: '',
        brand_id: '',
        supplier_id: ''
    };
    
    // Limpiar variables de búsqueda de cliente
    searchingClient.value = false;
    clientFound.value = false;
    clientNotFound.value = false;
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
        searchTimeout.value = null;
    }
};

const loadSaleData = async () => {
    try {
        const response = await fetch(route('employee.sales.create'), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        allProducts.value = data.props.products || [];
        categories.value = data.props.categories || [];
        brands.value = data.props.brands || [];
        suppliers.value = data.props.suppliers || [];
    } catch (error) {
        console.error('Error loading sale data:', error);
        allProducts.value = [];
        categories.value = [];
        brands.value = [];
        suppliers.value = [];
    }
};

const addProductToSale = (product) => {
    saleForm.products.push({
        id: product.id,
        name: product.name,
        price: product.price,
        quantity: 1,
        stock: product.stock
    });
};

const removeProductFromSale = (index) => {
    saleForm.products.splice(index, 1);
};

const updateSubtotal = (index) => {
    // La reactividad de Vue se encarga del cálculo automático
};

const calculateTotal = () => {
    return saleForm.products.reduce((total, item) => {
        return total + (item.price * item.quantity);
    }, 0);
};

// Función para buscar cliente por DNI
const searchClientByDni = () => {
    const dni = saleForm.customer_dni;
    
    // Limpiar timeout anterior
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    
    // Resetear estados
    clientFound.value = false;
    clientNotFound.value = false;
    searchingClient.value = false;
    
    // Validar DNI (debe tener 8 dígitos)
    if (!dni || dni.length !== 8 || !/^\d{8}$/.test(dni)) {
        if (dni && dni.length > 0) {
            // Si hay texto pero no es válido, limpiar el nombre
            saleForm.customer_name = '';
        }
        return;
    }
    
    // Buscar con delay para evitar muchas peticiones
    searchTimeout.value = setTimeout(async () => {
        searchingClient.value = true;
        
        try {
            const response = await fetch(route('employee.clients.search-dni'), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dni: dni })
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.client) {
                    // Cliente encontrado
                    clientFound.value = true;
                    clientNotFound.value = false;
                    saleForm.customer_name = data.client.full_name || data.client.name || '';
                } else {
                    // Cliente no encontrado
                    clientFound.value = false;
                    clientNotFound.value = true;
                    saleForm.customer_name = '';
                }
            } else {
                // Error HTTP
                console.error('Error HTTP:', response.status);
                clientFound.value = false;
                clientNotFound.value = true;
                saleForm.customer_name = '';
            }
        } catch (error) {
            console.error('Error searching client:', error);
            clientFound.value = false;
            clientNotFound.value = true;
            saleForm.customer_name = '';
        } finally {
            searchingClient.value = false;
        }
    }, 500); // Delay de 500ms
};

const submitSale = () => {
    saleForm.total = calculateTotal();
    
    const items = saleForm.products.map(product => ({
        product_id: product.id,
        quantity: product.quantity,
        price: product.price
    }));

    saleForm.transform(data => ({
        items: items,
        customer_name: data.customer_name,
        total: data.total
    })).post(route('employee.sales.store'), {
        onSuccess: () => {
            closeCreateModal();
            router.reload();
        },
        onError: (errors) => {
            console.error('Error creating sale:', errors);
        }
    });
};

const viewSaleDetails = async (sale) => {
    if (!sale || !sale.id) {
        console.error('Sale data is invalid');
        return;
    }
    
    try {
        const response = await fetch(route('employee.sales.show', sale.id), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        selectedSale.value = data.props.sale;
        showDetailsModal.value = true;
    } catch (error) {
        console.error('Error loading sale details:', error);
    }
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedSale.value = null;
};

const printTicket = (sale) => {
    if (!sale || !sale.id) {
        console.error('Sale data is invalid');
        return;
    }
    
    try {
        window.open(route('employee.sales.ticket', sale.id) + '?print=1', '_blank');
    } catch (error) {
        console.error('Error opening ticket:', error);
    }
};

const downloadTicket = (sale) => {
    if (!sale || !sale.id) {
        console.error('Sale data is invalid');
        return;
    }
    
    try {
        const link = document.createElement('a');
        link.href = route('employee.sales.ticket', sale.id) + '?download=1';
        link.download = `ticket-${sale.code}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error('Error downloading ticket:', error);
    }
};
</script> 