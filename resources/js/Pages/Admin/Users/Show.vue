<template>
    <Head :title="`Usuario: ${user.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('admin.users.index')" class="text-gray-500 hover:text-gray-700">
                        <ArrowLeftIcon class="h-5 w-5" />
                    </Link>
                    <div>
                        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                            Detalles del Usuario
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">{{ user.name }} - {{ user.role_label }}</p>
                    </div>
                </div>
                <button
                    @click="generatePDF"
                    class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg font-medium hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                >
                    <DocumentArrowDownIcon class="h-4 w-4" />
                    <span>Generar PDF</span>
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Información del Usuario -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="p-2 bg-blue-200 rounded-lg mr-3">
                                <UserIcon class="h-5 w-5 text-blue-600" />
                            </div>
                            Información del Usuario
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                    'bg-purple-100 text-purple-800': user.role === 'admin',
                                    'bg-blue-100 text-blue-800': user.role === 'employee'
                                }">
                                    {{ user.role_label }}
                                </span>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Fecha de Registro</label>
                                <p class="text-lg font-semibold text-gray-900">{{ user.created_at }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Estado</label>
                                <span :class="{
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                    'bg-green-100 text-green-800': user.email_verified_at,
                                    'bg-yellow-100 text-yellow-800': !user.email_verified_at
                                }">
                                    {{ user.email_verified_at ? 'Verificado' : 'Pendiente' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Ventas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Total Ventas</p>
                                <p class="text-2xl font-bold">{{ statistics.total_sales }}</p>
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
                                <p class="text-2xl font-bold">{{ statistics.completed_sales }}</p>
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
                                <p class="text-2xl font-bold">S/ {{ statistics.total_revenue.toFixed(2) }}</p>
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
                                <p class="text-2xl font-bold">S/ {{ statistics.average_sale.toFixed(2) }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ChartBarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Ventas -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                <DocumentTextIcon class="h-5 w-5 text-gray-600" />
                            </div>
                            Historial de Ventas
                            <span class="ml-2 text-sm text-gray-500">({{ sales.length }} ventas)</span>
                        </h3>
                    </div>

                    <div class="p-6">
                        <div v-if="sales.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <ShoppingCartIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay ventas registradas</p>
                            <p class="text-sm text-gray-400 mt-1">Este usuario aún no ha realizado ventas</p>
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
                                            <span class="text-sm font-semibold text-gray-900">S/ {{ sale.total.toFixed(2) }}</span>
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
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { 
    ArrowLeftIcon, 
    UserIcon, 
    ShoppingCartIcon, 
    CheckCircleIcon, 
    CurrencyDollarIcon, 
    ChartBarIcon,
    DocumentTextIcon,
    DocumentArrowDownIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    user: Object,
    sales: Array,
    statistics: Object
})

const generatePDF = () => {
    window.open(route('admin.users.sales-report', props.user.id), '_blank')
}
</script> 