<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div 
                class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                @click="$emit('close')"
            ></div>

            <!-- Modal -->
            <div class="inline-block w-full max-w-6xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-white bg-opacity-20 rounded-full">
                                <UserIcon class="h-8 w-8 text-white" />
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">
                                    Detalles del Usuario
                                </h3>
                                <p class="text-blue-100 mt-1" v-if="user">
                                    {{ user.name }} - {{ user.role === 'admin' ? 'Administrador' : 'Empleado' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button
                                @click="generatePDF"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2"
                                v-if="user"
                            >
                                <DocumentArrowDownIcon class="h-4 w-4" />
                                <span>PDF</span>
                            </button>
                            <button
                                @click="$emit('close')"
                                class="text-white hover:text-gray-200 transition-colors"
                            >
                                <XMarkIcon class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 max-h-[70vh] overflow-y-auto">
                    <div v-if="loading" class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-600">Cargando información...</span>
                    </div>

                    <div v-else-if="user" class="space-y-8">
                        <!-- Información del Usuario -->
                        <div class="bg-gray-50 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <UserIcon class="h-5 w-5 text-blue-600" />
                                </div>
                                Información Personal
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">ID</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ user.formatted_id }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Nombre</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ user.name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Email</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ user.email }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Rol</label>
                                    <span :class="{
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium': true,
                                        'bg-purple-100 text-purple-800': user.role === 'admin',
                                        'bg-blue-100 text-blue-800': user.role === 'employee'
                                    }">
                                        {{ user.role === 'admin' ? 'Administrador' : 'Empleado' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Registro</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ user.created_at }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Estado</label>
                                    <span :class="{
                                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium': true,
                                        'bg-green-100 text-green-800': user.email_verified_at,
                                        'bg-yellow-100 text-yellow-800': !user.email_verified_at
                                    }">
                                        {{ user.email_verified_at ? 'Verificado' : 'Pendiente' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas de Ventas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                                        <p class="text-2xl font-bold">{{ statistics.total_sales }}</p>
                                    </div>
                                    <ShoppingCartIcon class="h-8 w-8 opacity-80" />
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm font-medium">Completadas</p>
                                        <p class="text-2xl font-bold">{{ statistics.completed_sales }}</p>
                                    </div>
                                    <CheckCircleIcon class="h-8 w-8 opacity-80" />
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm font-medium">Ingresos</p>
                                        <p class="text-xl font-bold">S/ {{ formatNumber(statistics.total_revenue) }}</p>
                                    </div>
                                    <CurrencyDollarIcon class="h-8 w-8 opacity-80" />
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-orange-100 text-sm font-medium">Promedio</p>
                                        <p class="text-xl font-bold">S/ {{ formatNumber(statistics.average_sale) }}</p>
                                    </div>
                                    <ChartBarIcon class="h-8 w-8 opacity-80" />
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Ventas -->
                        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                        <DocumentTextIcon class="h-5 w-5 text-gray-600" />
                                    </div>
                                    Historial de Ventas
                                    <span class="ml-2 text-sm text-gray-500">({{ sales.length }} ventas)</span>
                                </h4>
                            </div>

                            <div class="p-6">
                                <div v-if="sales.length === 0" class="text-center py-12">
                                    <div class="p-4 bg-gray-100 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                                        <ShoppingCartIcon class="h-10 w-10 text-gray-400" />
                                    </div>
                                    <p class="text-gray-500 font-medium text-lg">No hay ventas registradas</p>
                                    <p class="text-sm text-gray-400 mt-2">Este usuario aún no ha realizado ventas</p>
                                </div>

                                <div v-else class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Código
                                                </th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Fecha
                                                </th>
                                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Cliente
                                                </th>
                                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Total
                                                </th>
                                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Items
                                                </th>
                                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr v-for="sale in sales" :key="sale.id" class="hover:bg-gray-50 transition-colors">
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center">
                                                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                                            <DocumentTextIcon class="h-4 w-4 text-blue-600" />
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-900">{{ sale.code }}</span>
                                                    </div>
                                                </td>
                                                <td class="py-4 px-4 text-sm text-gray-600">
                                                    {{ sale.created_at }}
                                                </td>
                                                <td class="py-4 px-4 text-sm text-gray-600">
                                                    <span v-if="sale.client">
                                                        {{ sale.client.full_name }}
                                                        <span class="text-xs text-gray-400">({{ sale.client.dni }})</span>
                                                    </span>
                                                    <span v-else class="text-gray-400">Sin cliente</span>
                                                </td>
                                                <td class="py-4 px-4 text-right">
                                                    <span class="text-sm font-semibold text-gray-900">S/ {{ formatNumber(sale.total) }}</span>
                                                </td>
                                                <td class="py-4 px-4 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ sale.items_count }} items
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4 text-center">
                                                    <span :class="{
                                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                                        'bg-green-100 text-green-800': sale.status === 'completed',
                                                        'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                                        'bg-red-100 text-red-800': sale.status === 'cancelled'
                                                    }">
                                                        <div :class="{
                                                            'w-1.5 h-1.5 rounded-full mr-1.5': true,
                                                            'bg-green-400': sale.status === 'completed',
                                                            'bg-yellow-400': sale.status === 'pending',
                                                            'bg-red-400': sale.status === 'cancelled'
                                                        }"></div>
                                                        {{ sale.status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <div class="flex justify-end space-x-3">
                        <button
                            @click="$emit('close')"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { 
    UserIcon, 
    ShoppingCartIcon, 
    CheckCircleIcon, 
    CurrencyDollarIcon, 
    ChartBarIcon,
    DocumentTextIcon,
    DocumentArrowDownIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    user: {
        type: Object,
        default: null
    },
    sales: {
        type: Array,
        default: () => []
    },
    statistics: {
        type: Object,
        default: () => ({
            total_sales: 0,
            total_revenue: 0,
            completed_sales: 0,
            average_sale: 0
        })
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

const generatePDF = () => {
    if (props.user) {
        window.open(route('admin.users.sales-report', props.user.id), '_blank')
    }
}

const formatNumber = (value) => {
    if (value === null || value === undefined) {
        return '0.00'
    }
    
    const numValue = parseFloat(value)
    if (isNaN(numValue)) {
        return '0.00'
    }
    
    return numValue.toFixed(2)
}
</script>

<style scoped>
/* Animaciones suaves */
.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s ease;
}
.modal-enter-from, .modal-leave-to {
    opacity: 0;
}
</style> 