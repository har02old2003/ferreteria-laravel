<template>
    <AdminLayout :title="`Ventas de ${user.name}`">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ventas de {{ user.name }}
                </h2>
                <Link :href="route('admin.users.index')" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Volver a Usuarios
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="sales.data.length === 0" class="text-center py-8">
                            <p class="text-gray-500">Este usuario aún no ha realizado ventas.</p>
                        </div>
                        <div v-else>
                            <!-- Lista de ventas -->
                            <div v-for="sale in sales.data" :key="sale.id" class="mb-8 bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            Venta #{{ sale.id }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ sale.created_at }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-medium text-gray-900">Total: S/. {{ sale.total }}</p>
                                        <p class="text-sm text-gray-500">{{ sale.items_count }} productos</p>
                                    </div>
                                </div>

                                <!-- Detalles de la venta -->
                                <div class="mt-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Producto
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Cantidad
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Precio
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Subtotal
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="item in sale.items" :key="item.product_name">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ item.product_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ item.quantity }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    S/. {{ item.price }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    S/. {{ item.subtotal }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Paginación -->
                            <Pagination :links="sales.links" class="mt-6" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'

export default defineComponent({
    components: {
        AdminLayout,
        Link,
        Pagination
    },

    props: {
        user: {
            type: Object,
            required: true
        },
        sales: {
            type: Object,
            required: true
        }
    }
})
</script> 