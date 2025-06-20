<template>
    <Head title="Historial de Ventas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Ventas
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona las ventas de la ferretería</p>
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
                                <p class="text-2xl font-bold">S/ {{ totalRevenue.toFixed(2) }}</p>
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
                                <p class="text-2xl font-bold">S/ {{ averageSale.toFixed(2) }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ChartBarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Ventas -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <DocumentTextIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Historial de Ventas
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ safeSales.length }} ventas registradas</span>
                                <button
                                    @click="openCreateModal"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                                >
                                    <PlusIcon class="h-4 w-4" />
                                    <span>Nueva Venta</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="safeSales.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <ShoppingCartIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay ventas registradas aún</p>
                            <p class="text-sm text-gray-400 mt-1">Comienza creando una nueva venta</p>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Crear Primera Venta
                            </button>
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
                                            Estado
                                        </th>
                                        <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="sale in safeSales" :key="sale.id || Math.random()" class="hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                                    <DocumentTextIcon class="h-4 w-4 text-blue-600" />
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ sale.code || 'SIN-CODIGO' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-600">
                                            {{ formatDate(sale.created_at) }}
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-600">
                                            <span v-if="sale.client">
                                                {{ sale.client.full_name }}
                                                <span class="text-xs text-gray-400">(DNI: {{ sale.client.dni }})</span>
                                            </span>
                                            <span v-else class="text-gray-400">Sin cliente asignado</span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <span class="text-sm font-semibold text-gray-900">{{ sale.total }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span :class="{
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium': true,
                                                'bg-green-100 text-green-800': sale.status === 'completed',
                                                'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                                                'bg-red-100 text-red-800': sale.status === 'cancelled',
                                                'bg-gray-100 text-gray-800': !sale.status
                                            }">
                                                <div :class="{
                                                    'w-1.5 h-1.5 rounded-full mr-1.5': true,
                                                    'bg-green-400': sale.status === 'completed',
                                                    'bg-yellow-400': sale.status === 'pending',
                                                    'bg-red-400': sale.status === 'cancelled',
                                                    'bg-gray-400': !sale.status
                                                }"></div>
                                                {{ sale.status_label }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button
                                                    v-if="sale.id"
                                                    @click="viewSaleDetails(sale)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                    :title="`Ver detalles de ${sale.code}`"
                                                >
                                                    <EyeIcon class="h-3 w-3 mr-1" />
                                                    Ver
                                                </button>
                                                <button
                                                    v-if="sale.status === 'completed' && sale.id"
                                                    @click="printTicket(sale)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded-lg hover:bg-green-200 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1"
                                                    :title="`Imprimir ticket de ${sale.code}`"
                                                >
                                                    <PrinterIcon class="h-3 w-3 mr-1" />
                                                    Imprimir
                                                </button>
                                                <button
                                                    v-if="sale.id"
                                                    @click="confirmDelete(sale)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                    :title="`Eliminar ${sale.code}`"
                                                >
                                                    <TrashIcon class="h-3 w-3 mr-1" />
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
                        <InputLabel value="Información del Cliente" />
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="client_dni" value="DNI del Cliente" />
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <TextInput
                                        id="client_dni"
                                        v-model="clientForm.dni"
                                        type="text"
                                        maxlength="8"
                                        placeholder="Ingrese DNI (8 dígitos)"
                                        class="flex-1 rounded-none rounded-l-md"
                                        @input="handleDniInput"
                                    />
                                    <ActionButton
                                        variant="primary"
                                        text="Buscar"
                                        :loading="searchingClient"
                                        :disabled="clientForm.dni.length !== 8 || searchingClient"
                                        @click="searchClientByDni"
                                        class="rounded-none rounded-r-md"
                                    />
                                </div>
                                <p v-if="clientSearchMessage" class="mt-1 text-xs" :class="clientSearchMessage.type === 'error' ? 'text-red-600' : 'text-green-600'">
                                    {{ clientSearchMessage.text }}
                                </p>
                            </div>
                            <div v-if="clientForm.first_name">
                                <InputLabel value="Datos del Cliente" />
                                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm font-medium text-gray-900">{{ clientForm.full_name }}</p>
                                    <p class="text-xs text-gray-500">DNI: {{ clientForm.dni }}</p>
                                    <p v-if="clientForm.phone" class="text-xs text-gray-500">Teléfono: {{ clientForm.phone }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campos adicionales del cliente (opcionales) -->
                        <div v-if="clientForm.first_name && clientForm.source === 'api'" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="client_phone" value="Teléfono (Opcional)" />
                                <TextInput
                                    id="client_phone"
                                    v-model="clientForm.phone"
                                    type="text"
                                    placeholder="Número de teléfono"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div>
                                <InputLabel for="client_email" value="Email (Opcional)" />
                                <TextInput
                                    id="client_email"
                                    v-model="clientForm.email"
                                    type="email"
                                    placeholder="Correo electrónico"
                                    class="mt-1 block w-full"
                                />
                            </div>
                            <div class="md:col-span-2">
                                <InputLabel for="client_address" value="Dirección (Opcional)" />
                                <TextInput
                                    id="client_address"
                                    v-model="clientForm.address"
                                    type="text"
                                    placeholder="Dirección completa"
                                    class="mt-1 block w-full"
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
                                            <p class="text-xs text-gray-500">{{ product.category }} - {{ product.brand }}</p>
                                            <p class="text-xs text-gray-400">SKU: {{ product.sku }} | Stock: {{ product.stock }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">S/ {{ product.price.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</p>
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
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">S/ {{ item.price.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
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
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">S/ {{ (item.price * item.quantity).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
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
                            <span class="text-2xl font-bold text-gray-900">S/ {{ calculateTotal().toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</span>
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
        >
            <div class="p-6">
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
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale?.status }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Usuario</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ selectedSale?.user?.name || 'No asignado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cliente</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <span v-if="selectedSale?.client">
                                        {{ selectedSale.client.full_name }}
                                        <span class="text-xs text-gray-500">(DNI: {{ selectedSale.client.dni }})</span>
                                    </span>
                                    <span v-else class="text-gray-400">Sin cliente asignado</span>
                                </dd>
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
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">S/ {{ Number(item.price || 0).toFixed(2) }}</td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">S/ {{ Number(item.subtotal || 0).toFixed(2) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th scope="row" colspan="3" class="pl-4 pr-3 pt-4 text-right text-sm font-semibold text-gray-900">Total</th>
                                                <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold text-gray-900">{{ selectedSale?.total || 'S/ 0.00' }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <ActionButton
                        variant="secondary"
                        text="Cerrar"
                        @click="closeDetailsModal"
                    />
                </div>
            </div>
        </Modal>

        <!-- Modal de Confirmación de Eliminación -->
        <Modal
            :show="showDeleteModal"
            title="Confirmar Eliminación"
            @close="closeDeleteModal"
        >
            <div class="p-6">
                <p class="text-sm text-gray-600">
                    ¿Estás seguro de que deseas eliminar esta venta? Esta acción no se puede deshacer.
                </p>

                <div class="mt-6 flex justify-end space-x-3">
                    <ActionButton
                        variant="secondary"
                        text="Cancelar"
                        @click="closeDeleteModal"
                    />
                    <ActionButton
                        variant="danger"
                        text="Eliminar"
                        :loading="deleteForm.processing"
                        :disabled="deleteForm.processing"
                        @click="deleteSale"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { PlusIcon, EyeIcon, TrashIcon, PrinterIcon, ShoppingCartIcon, CheckCircleIcon, CurrencyDollarIcon, ChartBarIcon, DocumentTextIcon } from '@heroicons/vue/24/outline'
import { useNotifications } from '@/Composables/useNotifications'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/Common/PageHeader.vue'
import DataTable from '@/Components/Common/DataTable.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'

const props = defineProps({
    sales: {
        type: Array,
        required: true
    }
})

// Computed properties seguras
const safeSales = computed(() => {
    if (!props.sales || !Array.isArray(props.sales)) {
        return [];
    }
    return props.sales;
});

const completedSales = computed(() => {
    return safeSales.value.filter(sale => sale.status === 'completed').length;
});

const totalRevenue = computed(() => {
    return safeSales.value
        .filter(sale => sale.status === 'completed')
        .reduce((total, sale) => {
            const saleTotal = parseFloat(sale.total?.toString().replace(/[^\d.-]/g, '') || 0);
            return total + saleTotal;
        }, 0);
});

const averageSale = computed(() => {
    const completed = completedSales.value;
    return completed > 0 ? totalRevenue.value / completed : 0;
});

// Función para formatear fechas
const formatDate = (dateString) => {
    if (!dateString) return 'Sin fecha';
    
    // Si ya viene formateado desde el backend, devolverlo tal como está
    if (typeof dateString === 'string' && dateString.includes('/')) {
        return dateString;
    }
    
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return 'Fecha inválida';
        }
        return date.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return 'Fecha inválida';
    }
};

const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const showDeleteModal = ref(false)
const selectedSale = ref(null)

// Datos para el modal de nueva venta
const allProducts = ref([])
const categories = ref([])
const brands = ref([])
const suppliers = ref([])

const filters = ref({
    category_id: '',
    brand_id: '',
    supplier_id: ''
})

const saleForm = useForm({
    products: [],
    client_id: null
})

const deleteForm = useForm({})

// Variables para manejo de clientes
const clientForm = ref({
    dni: '',
    first_name: '',
    last_name: '',
    full_name: '',
    phone: '',
    email: '',
    address: '',
    source: null // 'database' o 'api'
})

const searchingClient = ref(false)
const clientSearchMessage = ref(null)

const { success, error } = useNotifications()

// Computed properties para las opciones de los filtros
const categoryOptions = computed(() => [
    { id: '', name: 'Todas las categorías' },
    ...categories.value.map(cat => ({ id: cat.id, name: cat.name }))
])

const brandOptions = computed(() => [
    { id: '', name: 'Todas las marcas' },
    ...brands.value.map(brand => ({ id: brand.id, name: brand.name }))
])

const supplierOptions = computed(() => [
    { id: '', name: 'Todos los proveedores' },
    ...suppliers.value.map(supplier => ({ id: supplier.id, name: supplier.name }))
])

const filteredProducts = computed(() => {
    let filtered = allProducts.value

    if (filters.value.category_id) {
        filtered = filtered.filter(p => p.category_id === filters.value.category_id)
    }
    if (filters.value.brand_id) {
        filtered = filtered.filter(p => p.brand_id === filters.value.brand_id)
    }
    if (filters.value.supplier_id) {
        filtered = filtered.filter(p => p.supplier_id === filters.value.supplier_id)
    }

    // Excluir productos ya agregados a la venta
    const addedProductIds = saleForm.products.map(p => p.id)
    filtered = filtered.filter(p => !addedProductIds.includes(p.id))

    return filtered
})

const openCreateModal = async () => {
    // Cargar productos y datos necesarios
    await loadSaleData()
    showCreateModal.value = true
}

const closeCreateModal = () => {
    showCreateModal.value = false
    saleForm.reset()
    filters.value = {
        category_id: '',
        brand_id: '',
        supplier_id: ''
    }
    // Reset client form
    clientForm.value = {
        dni: '',
        first_name: '',
        last_name: '',
        full_name: '',
        phone: '',
        email: '',
        address: '',
        source: null
    }
    clientSearchMessage.value = null
}

const loadSaleData = async () => {
    try {
        const response = await fetch(route('admin.sales.create'), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        const data = await response.json()
        
        allProducts.value = data.props.products || []
        categories.value = data.props.categories || []
        brands.value = data.props.brands || []
        suppliers.value = data.props.suppliers || []
    } catch (error) {
        console.error('Error loading sale data:', error)
        // Fallback: cargar datos directamente
        allProducts.value = []
        categories.value = []
        brands.value = []
        suppliers.value = []
    }
}

const addProductToSale = (product) => {
    saleForm.products.push({
        id: product.id,
        name: product.name,
        price: product.price,
        quantity: 1,
        stock: product.stock
    })
}

const removeProductFromSale = (index) => {
    saleForm.products.splice(index, 1)
}

const updateSubtotal = (index) => {
    // La reactividad de Vue se encarga del cálculo automático
}

const calculateTotal = () => {
    return saleForm.products.reduce((total, item) => {
        return total + (item.price * item.quantity)
    }, 0)
}

// Funciones para manejo de clientes
const handleDniInput = () => {
    // Limpiar mensaje anterior
    clientSearchMessage.value = null
    
    // Solo permitir números
    clientForm.value.dni = clientForm.value.dni.replace(/\D/g, '')
    
    // Limpiar datos del cliente si se modifica el DNI
    if (clientForm.value.dni.length !== 8) {
        clientForm.value.first_name = ''
        clientForm.value.last_name = ''
        clientForm.value.full_name = ''
        clientForm.value.phone = ''
        clientForm.value.email = ''
        clientForm.value.address = ''
        clientForm.value.source = null
    }
}

const searchClientByDni = async () => {
    if (clientForm.value.dni.length !== 8) {
        clientSearchMessage.value = {
            type: 'error',
            text: 'El DNI debe tener exactamente 8 dígitos'
        }
        return
    }

    searchingClient.value = true
    clientSearchMessage.value = null

    try {
        const response = await fetch(route('admin.clients.search-dni'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                dni: clientForm.value.dni
            })
        })

        const data = await response.json()

        if (data.success) {
            // Llenar los datos del cliente
            clientForm.value.first_name = data.client.first_name
            clientForm.value.last_name = data.client.last_name
            clientForm.value.full_name = data.client.full_name
            clientForm.value.phone = data.client.phone || ''
            clientForm.value.email = data.client.email || ''
            clientForm.value.address = data.client.address || ''
            clientForm.value.source = data.source

            if (data.source === 'database') {
                clientSearchMessage.value = {
                    type: 'success',
                    text: 'Cliente encontrado en la base de datos'
                }
                // Si existe en BD, usar su ID
                saleForm.client_id = data.client.id
            } else {
                clientSearchMessage.value = {
                    type: 'success',
                    text: 'Datos obtenidos de RENIEC. Se creará el cliente al guardar la venta.'
                }
                saleForm.client_id = null
            }
        } else {
            clientSearchMessage.value = {
                type: 'error',
                text: data.message || 'No se encontraron datos para este DNI'
            }
        }
    } catch (error) {
        console.error('Error searching client:', error)
        clientSearchMessage.value = {
            type: 'error',
            text: 'Error al buscar el cliente. Intente nuevamente.'
        }
    } finally {
        searchingClient.value = false
    }
}

const submitSale = () => {
    const saleData = {
        products: saleForm.products.map(item => ({
            id: item.id,
            quantity: item.quantity
        })),
        client_id: saleForm.client_id,
        client_data: clientForm.value.first_name ? {
            dni: clientForm.value.dni,
            first_name: clientForm.value.first_name,
            last_name: clientForm.value.last_name,
            full_name: clientForm.value.full_name,
            phone: clientForm.value.phone,
            email: clientForm.value.email,
            address: clientForm.value.address,
            source: clientForm.value.source
        } : null
    }

    saleForm.transform(() => saleData).post(route('admin.sales.store'), {
        onSuccess: () => {
            closeCreateModal()
            success('Venta creada', 'La venta se ha registrado correctamente. Puedes imprimir el ticket desde la lista de ventas.')
        },
        onError: () => {
            error('Error al crear venta', 'No se pudo registrar la venta')
        }
    })
}

const viewSaleDetails = (sale) => {
    selectedSale.value = sale
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedSale.value = null
}

const confirmDelete = (sale) => {
    selectedSale.value = sale
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedSale.value = null
}

const deleteSale = () => {
    deleteForm.delete(route('admin.sales.destroy', selectedSale.value.id), {
        onSuccess: () => {
            closeDeleteModal()
            success('Venta eliminada', 'La venta se ha eliminado correctamente')
        },
        onError: () => {
            error('Error al eliminar', 'No se pudo eliminar la venta')
        }
    })
}

const handleSearch = (query) => {
    router.get(
        route('admin.sales.index'),
        { search: query },
        { preserveState: true, preserveScroll: true }
    )
}

const filterProducts = () => {
    // La reactividad de Vue se encarga del filtrado automático
}

const printTicket = (sale) => {
    // Abrir el ticket en una nueva ventana optimizada para impresión
    const ticketUrl = route('admin.sales.ticket', sale.id)
    const printWindow = window.open(
        ticketUrl, 
        'ticket_' + sale.id, 
        'width=400,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no,location=no,status=no'
    )
    
    // Enfocar la ventana del ticket
    if (printWindow) {
        printWindow.focus()
        // Intentar imprimir automáticamente después de cargar
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print()
            }, 500)
        }
    }
}
</script> 