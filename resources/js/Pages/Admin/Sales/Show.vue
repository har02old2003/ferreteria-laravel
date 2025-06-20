<template>
    <Head title="Detalles de Venta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalles de Venta #{{ sale.code }}
                </h2>
                <Link
                    :href="route('admin.sales.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                    Volver a Ventas
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <!-- Información general de la venta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Información de la Venta</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><span class="font-medium">Código:</span> {{ sale.code }}</p>
                                <p><span class="font-medium">Fecha:</span> {{ formatDate(sale.created_at) }}</p>
                                <p><span class="font-medium">Estado:</span> 
                                    <span :class="{
                                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800': sale.status === 'completed',
                                        'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                        'bg-red-100 text-red-800': sale.status === 'cancelled'
                                    }">
                                        {{ formatStatus(sale.status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Información del Cliente</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><span class="font-medium">Nombre:</span> {{ sale.user.name }}</p>
                                <p><span class="font-medium">Email:</span> {{ sale.user.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Precio Unitario
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in sale.products" :key="product.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ product.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ product.quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/. {{ formatPrice(product.price) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            S/. {{ formatPrice(product.subtotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                            Total:
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            S/. {{ formatPrice(sale.total) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <Link
                            :href="route('admin.sales.ticket', sale.id)"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                            target="_blank"
                        >
                            Imprimir Ticket
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    sale: {
        type: Object,
        required: true
    }
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatPrice = (price) => {
    return Number(price).toFixed(2)
}

const formatStatus = (status) => {
    const statusMap = {
        'completed': 'Completada',
        'pending': 'Pendiente',
        'cancelled': 'Cancelada'
    }
    return statusMap[status] || status
}
</script> 