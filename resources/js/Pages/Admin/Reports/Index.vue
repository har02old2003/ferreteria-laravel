<template>
    <Head title="Reportes y Exportaciones" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    📊 Centro de Reportes
                </h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <DocumentArrowDownIcon class="h-5 w-5" />
                    <span>Exportación de Datos</span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Estadísticas Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                                <p class="text-3xl font-bold">{{ stats.total_sales }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CurrencyDollarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium">Total Productos</p>
                                <p class="text-3xl font-bold">{{ stats.total_products }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CubeIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100 text-sm font-medium">Stock Bajo</p>
                                <p class="text-3xl font-bold">{{ stats.low_stock_products }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ExclamationTriangleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Total Clientes</p>
                                <p class="text-3xl font-bold">{{ stats.total_clients }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <UsersIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Exportaciones -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    
                    <!-- Reporte de Ventas -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <CurrencyDollarIcon class="h-6 w-6 text-blue-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Reporte de Ventas</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">
                                Exporta un reporte detallado de todas las ventas con información de clientes, productos y vendedores.
                            </p>
                            
                            <!-- Filtros de fecha -->
                            <div class="space-y-3 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                                    <input 
                                        type="date" 
                                        v-model="salesDateRange.start"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                                    <input 
                                        type="date" 
                                        v-model="salesDateRange.end"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                </div>
                            </div>
                            
                            <button 
                                @click="exportSales"
                                :disabled="exportingStates.sales"
                                class="w-full flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <ArrowDownTrayIcon v-if="!exportingStates.sales" class="h-5 w-5 mr-2" />
                                <span v-if="!exportingStates.sales" class="animate-spin h-5 w-5 mr-2 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ exportingStates.sales ? 'Exportando...' : 'Exportar Ventas' }}
                            </button>
                        </div>
                    </div>

                    <!-- Reporte de Stock Bajo -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                    <ExclamationTriangleIcon class="h-6 w-6 text-orange-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Productos Bajo Stock</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">
                                Lista completa de productos que necesitan reposición urgente con información detallada.
                            </p>
                            
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <ExclamationTriangleIcon class="h-5 w-5 text-orange-500 mr-2" />
                                    <span class="text-sm font-medium text-orange-800">
                                        {{ stats.low_stock_products }} productos necesitan atención
                                    </span>
                                </div>
                            </div>
                            
                            <button 
                                @click="exportLowStock"
                                :disabled="exportingStates.lowStock"
                                class="w-full flex items-center justify-center px-4 py-3 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <ArrowDownTrayIcon v-if="!exportingStates.lowStock" class="h-5 w-5 mr-2" />
                                <span v-if="exportingStates.lowStock" class="animate-spin h-5 w-5 mr-2 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ exportingStates.lowStock ? 'Exportando...' : 'Exportar Reporte' }}
                            </button>
                        </div>
                    </div>

                    <!-- Reporte de Dashboard -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow">
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <ChartBarIcon class="h-6 w-6 text-purple-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Resumen Ejecutivo</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">
                                Reporte ejecutivo con todas las métricas principales del dashboard para análisis rápido.
                            </p>
                            
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                                <div class="flex items-center">
                                    <ChartBarIcon class="h-5 w-5 text-purple-500 mr-2" />
                                    <span class="text-sm font-medium text-purple-800">
                                        Incluye todas las métricas principales
                                    </span>
                                </div>
                            </div>
                            
                            <button 
                                @click="exportDashboard"
                                :disabled="exportingStates.dashboard"
                                class="w-full flex items-center justify-center px-4 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <ArrowDownTrayIcon v-if="!exportingStates.dashboard" class="h-5 w-5 mr-2" />
                                <span v-if="exportingStates.dashboard" class="animate-spin h-5 w-5 mr-2 border-2 border-white border-t-transparent rounded-full"></span>
                                {{ exportingStates.dashboard ? 'Exportando...' : 'Exportar Dashboard' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <div class="p-2 bg-blue-100 rounded-lg mr-4">
                            <InformationCircleIcon class="h-6 w-6 text-blue-600" />
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-blue-900 mb-2">Información sobre Exportaciones</h4>
                            <ul class="text-blue-800 space-y-1 text-sm">
                                <li>• Los archivos se generan en formato CSV para máxima compatibilidad</li>
                                <li>• Los datos se exportan en tiempo real desde la base de datos</li>
                                <li>• Los nombres de archivo incluyen fecha y hora de generación</li>
                                <li>• Solo se incluyen datos de ventas completadas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';
import { useNotifications } from '@/Composables/useNotifications';
import {
    CurrencyDollarIcon,
    CubeIcon,
    UsersIcon,
    ExclamationTriangleIcon,
    ChartBarIcon,
    DocumentArrowDownIcon,
    ArrowDownTrayIcon,
    InformationCircleIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    }
});

const { success, error } = useNotifications();

// Estados de exportación
const exportingStates = ref({
    sales: false,
    lowStock: false,
    dashboard: false
});

// Rango de fechas para ventas
const salesDateRange = ref({
    start: new Date().toISOString().split('T')[0],
    end: new Date().toISOString().split('T')[0]
});

// Funciones de exportación
const exportSales = async () => {
    exportingStates.value.sales = true;
    
    try {
        const params = new URLSearchParams({
            start_date: salesDateRange.value.start,
            end_date: salesDateRange.value.end
        });
        
        window.location.href = `/admin/reports/export/sales?${params.toString()}`;
        
        success('Exportación Iniciada', 'El reporte de ventas se está descargando');
    } catch (err) {
        error('Error de Exportación', 'No se pudo generar el reporte de ventas');
    } finally {
        setTimeout(() => {
            exportingStates.value.sales = false;
        }, 2000);
    }
};

const exportLowStock = async () => {
    exportingStates.value.lowStock = true;
    
    try {
        window.location.href = '/admin/reports/export/low-stock';
        success('Exportación Iniciada', 'El reporte de stock bajo se está descargando');
    } catch (err) {
        error('Error de Exportación', 'No se pudo generar el reporte de stock');
    } finally {
        setTimeout(() => {
            exportingStates.value.lowStock = false;
        }, 2000);
    }
};

const exportDashboard = async () => {
    exportingStates.value.dashboard = true;
    
    try {
        window.location.href = '/admin/reports/export/dashboard';
        success('Exportación Iniciada', 'El reporte ejecutivo se está descargando');
    } catch (err) {
        error('Error de Exportación', 'No se pudo generar el reporte ejecutivo');
    } finally {
        setTimeout(() => {
            exportingStates.value.dashboard = false;
        }, 2000);
    }
};

onMounted(() => {
    // Establecer rango de fecha por defecto (último mes)
    const today = new Date();
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    
    salesDateRange.value.start = lastMonth.toISOString().split('T')[0];
    salesDateRange.value.end = today.toISOString().split('T')[0];
});
</script> 