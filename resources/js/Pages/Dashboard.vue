<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import WeeklySalesChart from '@/Components/WeeklySalesChart.vue';
import NotificationContainer from '@/Components/NotificationContainer.vue';
import { ref, onMounted, computed, watch } from 'vue';
import { useNotifications } from '@/Composables/useNotifications';
import {
  CurrencyDollarIcon,
  CubeIcon,
  TagIcon,
  UsersIcon,
  ExclamationTriangleIcon,
  ClockIcon,
  ChartBarIcon,
  ArrowUpIcon,
  ArrowDownIcon,
  DocumentArrowDownIcon
} from '@heroicons/vue/24/outline';

const weekOffset = ref(0);

// Refs para los contadores animados
const animatedSalesAmount = ref(0);
const animatedProductsCount = ref(0);
const animatedCustomersCount = ref(0);
const animatedCategoriesCount = ref(0);

// Sistema de notificaciones
const { showDashboardNotifications } = useNotifications();

const previousWeek = () => {
    weekOffset.value++;
};

const nextWeek = () => {
    if (weekOffset.value > 0) {
        weekOffset.value--;
    }
};

const currentWeek = () => {
    weekOffset.value = 0;
};

// Función para animar contadores
const animateCounter = (targetValue, currentRef, duration = 2000, isPrice = false) => {
  const startValue = 0;
  const increment = targetValue / (duration / 16); // 60 FPS
  let current = startValue;
  
  const timer = setInterval(() => {
    current += increment;
    if (current >= targetValue) {
      current = targetValue;
      clearInterval(timer);
    }
    currentRef.value = isPrice ? current : Math.floor(current);
  }, 16);
};

// Función para extraer el valor numérico del precio
const extractSalesValue = (salesAmount) => {
  if (!salesAmount) return 0;
  const match = salesAmount.match(/[\d,.]+/);
  if (match) {
    return parseFloat(match[0].replace(/,/g, ''));
  }
  return 0;
};

const props = defineProps({
  // Props unificados para admin y empleado
  totals: {
    type: Object,
    required: false,
    default: () => ({
      sales: { amount: 'S/ 0.00', percentage: 0, trend: 'up' },
      products: { count: 0, new: 0 },
      customers: { count: 0, new: 0 },
      categories: { count: 0, new: 0 }
    })
  },
  alerts: {
    type: Object,
    required: false,
    default: () => ({
      lowStock: [],
      expiring: []
    })
  },
  recentSales: {
    type: Array,
    required: false,
    default: () => []
  },
  topProducts: {
    type: Array,
    required: false,
    default: () => []
  },
  salesByDay: {
    type: Array,
    required: false,
    default: () => []
  },
  userRole: {
    type: String,
    required: false,
    default: 'employee'
  },
  error: {
    type: String,
    required: false
  }
});

// Computed para datos seguros con valores por defecto
const safeTotals = computed(() => ({
  sales: {
    amount: props.totals?.sales?.amount || 'S/ 0.00',
    percentage: props.totals?.sales?.percentage || 0,
    trend: props.totals?.sales?.trend || 'up'
  },
  products: {
    count: props.totals?.products?.count || 0,
    new: props.totals?.products?.new || 0
  },
  customers: {
    count: props.totals?.customers?.count || 0,
    new: props.totals?.customers?.new || 0
  },
  categories: {
    count: props.totals?.categories?.count || 0,
    new: props.totals?.categories?.new || 0
  }
}));

const safeAlerts = computed(() => ({
  lowStock: Array.isArray(props.alerts?.lowStock) ? props.alerts.lowStock : [],
  expiring: Array.isArray(props.alerts?.expiring) ? props.alerts.expiring : []
}));

const safeRecentSales = computed(() => Array.isArray(props.recentSales) ? props.recentSales : []);
const safeTopProducts = computed(() => Array.isArray(props.topProducts) ? props.topProducts : []);
const safeSalesByDay = computed(() => Array.isArray(props.salesByDay) ? props.salesByDay : []);

// Computed para determinar el título según el rol
const dashboardTitle = computed(() => {
  return props.userRole === 'admin' ? 'Dashboard - Administrador' : 'Dashboard - Empleado';
});

// Computed para formatear el monto de ventas animado
const formattedAnimatedSales = computed(() => {
  return `S/ ${animatedSalesAmount.value.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
});

const formatPrice = (price) => {
  if (price === null || price === undefined || isNaN(price)) return '0.00';
  return parseFloat(price).toFixed(2);
};

const formatStatus = (status) => {
  const statusMap = {
    'completed': 'Completada',
    'pending': 'Pendiente',
    'cancelled': 'Cancelada'
  };
  return statusMap[status] || status || 'Desconocido';
};

onMounted(() => {
  console.log('Dashboard mounted with props:', props);
  
  // Iniciar animaciones de contadores después de un pequeño delay para efecto dramático
  setTimeout(() => {
    const salesValue = extractSalesValue(safeTotals.value.sales.amount);
    animateCounter(salesValue, animatedSalesAmount, 2500, true);
    animateCounter(safeTotals.value.products.count, animatedProductsCount, 2000);
    animateCounter(safeTotals.value.customers.count, animatedCustomersCount, 2200);
    animateCounter(safeTotals.value.categories.count, animatedCategoriesCount, 1800);
  }, 300);

  // Mostrar notificaciones automáticas del dashboard
  showDashboardNotifications({
    totals: safeTotals.value,
    alerts: safeAlerts.value,
    topProducts: safeTopProducts.value,
    userRole: props.userRole
  });
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
                            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ dashboardTitle }}
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <ChartBarIcon class="h-5 w-5" />
                        <span>Panel de Control</span>
                    </div>
                    <!-- Botón de Reportes (solo para admin) -->
                    <Link 
                        v-if="userRole === 'admin'"
                        :href="route('admin.reports.index')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                        Exportar Reportes
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                <!-- Tarjetas de Estadísticas Modernas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Ventas Totales -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Ventas Totales</p>
                                <p class="text-3xl font-bold tracking-tight">{{ formattedAnimatedSales }}</p>
                                <div class="flex items-center mt-2">
                                    <ArrowUpIcon v-if="safeTotals.sales.trend === 'up'" class="h-4 w-4 text-green-300 mr-1 animate-bounce" />
                                    <ArrowDownIcon v-else class="h-4 w-4 text-red-300 mr-1 animate-bounce" />
                                    <span class="text-sm" :class="safeTotals.sales.trend === 'up' ? 'text-green-300' : 'text-red-300'">
                                        {{ safeTotals.sales.percentage }}% desde el último mes
                                    </span>
                                </div>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CurrencyDollarIcon class="h-8 w-8 animate-pulse" />
                            </div>
                        </div>
                    </div>

                    <!-- Total Productos -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Productos</p>
                                <p class="text-3xl font-bold tracking-tight">{{ animatedProductsCount.toLocaleString() }}</p>
                                <p class="text-sm text-green-200 mt-2">{{ safeTotals.products.new }} nuevos productos</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CubeIcon class="h-8 w-8 animate-pulse" />
                            </div>
                        </div>
                    </div>

                    <!-- Total Clientes -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Clientes</p>
                                <p class="text-3xl font-bold tracking-tight">{{ animatedCustomersCount.toLocaleString() }}</p>
                                <p class="text-sm text-purple-200 mt-2">{{ safeTotals.customers.new }} nuevos clientes</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <UsersIcon class="h-8 w-8 animate-pulse" />
                            </div>
                        </div>
                    </div>

                    <!-- Categorías -->
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium">Categorías</p>
                                <p class="text-3xl font-bold tracking-tight">{{ animatedCategoriesCount.toLocaleString() }}</p>
                                <p class="text-sm text-orange-200 mt-2">{{ safeTotals.categories.new }} nuevas categorías</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <TagIcon class="h-8 w-8 animate-pulse" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas Modernas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 alerts-section">
                    <!-- Productos con Bajo Stock -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                    <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Productos con Bajo Stock</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="alerts.lowStock.length === 0" class="text-center py-8">
                                <div class="p-3 bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">¡Excelente! No hay productos con stock bajo</p>
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                            <th class="text-center py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Actual</th>
                                            <th class="text-center py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock Mínimo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="product in alerts.lowStock" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 text-sm font-medium text-gray-900">{{ product.name }}</td>
                                            <td class="py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ product.stock }}
                                                </span>
                                            </td>
                                            <td class="py-4 text-center text-sm text-gray-500">{{ product.minimum_stock }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Próximos a Vencer -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                    <ClockIcon class="h-5 w-5 text-orange-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Productos Próximos a Vencer</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="alerts.expiring.length === 0" class="text-center py-8">
                                <div class="p-3 bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">No hay productos próximos a vencer</p>
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Producto</th>
                                            <th class="text-center py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Fecha de Caducidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="product in alerts.expiring" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 text-sm font-medium text-gray-900">{{ product.name }}</td>
                                            <td class="py-4 text-center">
                                                <span :class="{
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                                    'bg-red-100 text-red-800': product.days_until_expiration <= 30,
                                                    'bg-orange-100 text-orange-800': product.days_until_expiration > 30
                                                }">
                                                    {{ product.expiration_date }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Ventas Moderno -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <ChartBarIcon class="h-5 w-5 text-blue-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Análisis de Ventas - Últimos 7 Días</h3>
                            </div>
                            <div class="flex space-x-2">
                                <button @click="previousWeek" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                    ← Anterior
                                </button>
                                <button @click="currentWeek" class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Actual
                                </button>
                                <button @click="nextWeek" :disabled="weekOffset === 0" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors disabled:opacity-50">
                                    Siguiente →
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <WeeklySalesChart :week-offset="weekOffset" :sales-data="salesByDay" />
                    </div>
                </div>

                <!-- Tablas de Información -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Ventas Recientes -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <CurrencyDollarIcon class="h-5 w-5 text-green-600" />
                                </div>
                                Ventas Recientes
                            </h3>
                        </div>
                        <div class="p-6">
                            <div v-if="recentSales.length === 0" class="text-center py-8">
                                <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <CurrencyDollarIcon class="h-8 w-8 text-gray-400" />
                                </div>
                                <p class="text-gray-500 font-medium">No hay ventas registradas aún</p>
                            </div>
                            <div v-else class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                                            <th class="text-left py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Cliente</th>
                                            <th class="text-right py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                            <th class="text-center py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr v-for="sale in recentSales" :key="sale.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 text-sm font-medium text-gray-900">{{ sale.code }}</td>
                                            <td class="py-4 text-sm text-gray-600">{{ sale.user }}</td>
                                            <td class="py-4 text-sm font-semibold text-gray-900 text-right">S/ {{ formatPrice(sale.total) }}</td>
                                            <td class="py-4 text-center">
                                                <span :class="{
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                                    'bg-green-100 text-green-800': sale.status === 'completed',
                                                    'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                                    'bg-red-100 text-red-800': sale.status === 'cancelled'
                                                }">
                                                    {{ formatStatus(sale.status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Productos Más Vendidos (Solo para Admin) -->
                    <div v-if="userRole === 'admin' && topProducts.length > 0" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <CubeIcon class="h-5 w-5 text-purple-600" />
                                </div>
                                Productos Más Vendidos
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="product in topProducts" :key="product.name" class="relative">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">{{ product.name }}</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ Math.round(product.percentage) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all duration-500" 
                                             :style="{ width: product.percentage + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Rápido para Empleados -->
                    <div v-else class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                                    <ChartBarIcon class="h-5 w-5 text-indigo-600" />
                                </div>
                                Resumen de Actividad
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                            <CurrencyDollarIcon class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Ventas del Día</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ recentSales.length }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                                            <CubeIcon class="h-5 w-5 text-green-600" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Productos Disponibles</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ totals.products.count }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                            <ExclamationTriangleIcon class="h-5 w-5 text-yellow-600" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">Alertas Activas</span>
                                    </div>
                                    <span class="text-lg font-bold text-gray-900">{{ alerts.lowStock.length + alerts.expiring.length }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
    
    <!-- Contenedor de Notificaciones -->
    <NotificationContainer />
</template>
