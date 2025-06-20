<template>
    <Head title="Gestión de Productos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                        Productos
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona el inventario de productos</p>
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
                                <p class="text-2xl font-bold">{{ safeProducts.length }}</p>
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
                                <p class="text-2xl font-bold">{{ activeProducts }}</p>
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
                                <p class="text-2xl font-bold">{{ lowStockProducts }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <ExclamationTriangleIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium">Valor Inventario</p>
                                <p class="text-2xl font-bold">S/ {{ totalInventoryValue.toFixed(2) }}</p>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                                <CurrencyDollarIcon class="h-8 w-8" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Productos -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="p-2 bg-gray-200 rounded-lg mr-3">
                                    <CubeIcon class="h-5 w-5 text-gray-600" />
                                </div>
                                Inventario de Productos
                            </h3>
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-500">{{ safeProducts.length }} productos registrados</span>
                                <button
                                    @click="openCreateModal"
                                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 flex items-center space-x-2 shadow-sm"
                                >
                                    <PlusIcon class="h-4 w-4" />
                                    <span>Nuevo Producto</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div v-if="safeProducts.length === 0" class="text-center py-12">
                            <div class="p-3 bg-gray-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                <CubeIcon class="h-8 w-8 text-gray-400" />
                            </div>
                            <p class="text-gray-500 font-medium">No hay productos registrados aún</p>
                            <p class="text-sm text-gray-400 mt-1">Comienza agregando tu primer producto</p>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Crear Primer Producto
                            </button>
                        </div>

                        <div v-else>
                            <!-- Barra de búsqueda -->
                            <div class="mb-6">
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </div>
                                    <input
                                        type="text"
                                        placeholder="Buscar productos..."
                                        class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                        v-model="searchQuery"
                                        @input="handleSearch"
                                    />
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Imagen
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Producto
                                            </th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Categoría
                                            </th>
                                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Precio
                                            </th>
                                            <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Stock
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
                                        <tr v-for="product in filteredProducts" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                            <td class="py-4 px-4">
                                                <img 
                                                    v-if="product.image_url" 
                                                    :src="product.image_url" 
                                                    :alt="product.name"
                                                    class="h-10 w-10 rounded-lg object-cover"
                                                />
                                                <div 
                                                    v-else
                                                    class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center"
                                                >
                                                    <PhotoIcon class="h-5 w-5 text-gray-400" />
                                                </div>
                                            </td>
                                            <td class="py-4 px-4">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                                        <div class="text-xs text-gray-500">SKU: {{ product.sku }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-4 text-sm text-gray-600">
                                                {{ product.category }}
                                            </td>
                                            <td class="py-4 px-4 text-right">
                                                <span class="text-sm font-semibold text-gray-900">S/ {{ (product.price || 0).toFixed(2) }}</span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                        product.stock > (product.min_stock || 0)
                                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                            : product.stock > 0 
                                                                ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20'
                                                                : 'bg-red-50 text-red-700 ring-red-600/20'
                                                    ]"
                                                >
                                                    {{ product.stock }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <span
                                                    :class="[
                                                        'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                        product.is_active 
                                                            ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                            : 'bg-red-50 text-red-700 ring-red-600/20'
                                                    ]"
                                                >
                                                    <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                                                        product.is_active ? 'bg-green-600' : 'bg-red-600'
                                                    ]" />
                                                    {{ product.is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <button
                                                        @click="viewProductDetails(product)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                                                        :title="`Ver detalles de ${product.name}`"
                                                    >
                                                        <EyeIcon class="h-3 w-3 mr-1" />
                                                        Ver
                                                    </button>
                                                    <button
                                                        @click="openEditModal(product)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                                        :title="`Editar ${product.name}`"
                                                    >
                                                        <PencilIcon class="h-3 w-3 mr-1" />
                                                        Editar
                                                    </button>
                                                    <button
                                                        @click="confirmDelete(product)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                                                        :title="`Eliminar ${product.name}`"
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
        </div>

        <!-- Modal de Crear/Editar -->
        <Modal
            :show="showModal"
            :title="isEditing ? 'Editar Producto' : 'Nuevo Producto'"
            @close="closeModal"
            :max-width="'2xl'"
        >
            <form @submit.prevent="submitForm" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Nombre" required />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-2 block w-full"
                                required
                                autofocus
                                placeholder="Ingresa el nombre del producto"
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Descripción" />
                            <TextArea
                                id="description"
                                v-model="form.description"
                                class="mt-2 block w-full"
                                rows="3"
                                placeholder="Descripción detallada del producto"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="category_id" value="Categoría" required />
                            <SelectInput
                                id="category_id"
                                v-model="form.category_id"
                                :options="categories"
                                class="mt-2 block w-full"
                                required
                            />
                            <InputError :message="form.errors.category_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="brand_id" value="Marca" required />
                            <SelectInput
                                id="brand_id"
                                v-model="form.brand_id"
                                :options="brands"
                                class="mt-2 block w-full"
                                required
                            />
                            <InputError :message="form.errors.brand_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="supplier_id" value="Proveedor" required />
                            <SelectInput
                                id="supplier_id"
                                v-model="form.supplier_id"
                                :options="suppliers"
                                class="mt-2 block w-full"
                                required
                            />
                            <InputError :message="form.errors.supplier_id" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <InputLabel for="sku" value="SKU" required />
                            <TextInput
                                id="sku"
                                v-model="form.sku"
                                type="text"
                                class="mt-2 block w-full"
                                required
                                placeholder="Código único del producto"
                            />
                            <InputError :message="form.errors.sku" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="price" value="Precio (S/)" required />
                            <TextInput
                                id="price"
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="mt-2 block w-full"
                                required
                                placeholder="0.00"
                            />
                            <InputError :message="form.errors.price" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="stock" value="Stock Actual" required />
                            <TextInput
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                min="0"
                                class="mt-2 block w-full"
                                required
                                placeholder="0"
                            />
                            <InputError :message="form.errors.stock" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="image" value="Imagen" />
                            <input
                                type="file"
                                id="image"
                                ref="imageInput"
                                class="mt-2"
                                accept="image/*"
                                @change="handleImageChange"
                            />
                            <InputError :message="form.errors.image" class="mt-2" />
                            
                            <div v-if="imagePreview || form.image_url" class="mt-4">
                                <img 
                                    :src="imagePreview || form.image_url" 
                                    class="h-32 w-32 object-cover rounded-lg"
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="has_expiration" value="¿Tiene fecha de caducidad?" />
                            <div class="mt-2">
                                <Toggle
                                    id="has_expiration"
                                    v-model="form.has_expiration"
                                    :label="form.has_expiration ? 'Sí, tiene caducidad' : 'No tiene caducidad'"
                                />
                            </div>
                            <InputError :message="form.errors.has_expiration" class="mt-2" />
                        </div>

                        <div v-if="form.has_expiration">
                            <InputLabel for="expiration_date" value="Fecha de Caducidad" />
                            <TextInput
                                id="expiration_date"
                                v-model="form.expiration_date"
                                type="date"
                                class="mt-2 block w-full"
                                :min="new Date().toISOString().split('T')[0]"
                            />
                            <InputError :message="form.errors.expiration_date" class="mt-2" />
                            <p class="mt-1 text-xs text-gray-500">
                                Por defecto se establece 18 meses desde hoy
                            </p>
                        </div>

                        <div>
                            <InputLabel for="is_active" value="Estado" />
                            <div class="mt-2">
                                <Toggle
                                    id="is_active"
                                    v-model="form.is_active"
                                    :label="form.is_active ? 'Activo' : 'Inactivo'"
                                />
                            </div>
                            <InputError :message="form.errors.is_active" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <ActionButton
                        variant="secondary"
                        text="Cancelar"
                        @click="closeModal"
                    />
                    <ActionButton
                        type="submit"
                        text="Guardar"
                        :loading="form.processing"
                        :disabled="form.processing"
                    />
                </div>
            </form>
        </Modal>

        <!-- Modal de Confirmación de Eliminación -->
        <Modal
            :show="showDeleteModal"
            title="Confirmar Eliminación"
            @close="closeDeleteModal"
            :max-width="'lg'"
        >
            <div class="p-6">
                <div class="mb-8">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <ExclamationTriangleIcon class="h-6 w-6 text-red-600" aria-hidden="true" />
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">
                            ¿Estás seguro de eliminar este producto?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Esta acción no se puede deshacer. El producto se marcará como eliminado y no estará disponible en el sistema.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
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
                        @click="deleteProduct"
                    />
                </div>
            </div>
        </Modal>

        <!-- Modal de Detalles del Producto -->
        <Modal
            :show="showDetailsModal"
            title="Detalles del Producto"
            @close="closeDetailsModal"
            :max-width="'2xl'"
        >
            <div class="p-6" v-if="selectedProduct">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Información Principal -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información General</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">ID:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.formatted_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Nombre:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">SKU:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.sku }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Categoría:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.category }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Marca:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.brand }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Proveedor:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.supplier }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Descripción</h3>
                            <p class="text-sm text-gray-600">
                                {{ selectedProduct.description || 'Sin descripción' }}
                            </p>
                        </div>
                    </div>

                    <!-- Información Comercial y Stock -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información Comercial</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Precio:</span>
                                    <span class="text-sm font-semibold text-gray-900">S/ {{ selectedProduct.price?.toFixed(2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Stock Actual:</span>
                                    <span class="text-sm text-gray-900" :class="selectedProduct.stock <= selectedProduct.min_stock ? 'text-red-600 font-semibold' : ''">
                                        {{ selectedProduct.stock }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Stock Mínimo:</span>
                                    <span class="text-sm text-gray-900">{{ selectedProduct.min_stock }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Estado:</span>
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                            selectedProduct.is_active 
                                                ? 'bg-green-50 text-green-700 ring-green-600/20'
                                                : 'bg-red-50 text-red-700 ring-red-600/20'
                                        ]"
                                    >
                                        <div class="h-1.5 w-1.5 rounded-full mr-1.5" :class="[
                                            selectedProduct.is_active ? 'bg-green-600' : 'bg-red-600'
                                        ]" />
                                        {{ selectedProduct.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Fecha de Caducidad</h3>
                            <div v-if="selectedProduct.has_expiration && selectedProduct.expiration_date" class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Fecha de Vencimiento:</span>
                                    <span class="text-sm text-gray-900">
                                        {{ new Date(selectedProduct.expiration_date).toLocaleDateString('es-ES') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Días Restantes:</span>
                                    <span class="text-sm font-semibold" :class="getDaysUntilExpiration(selectedProduct.expiration_date) <= 30 ? 'text-red-600' : 'text-orange-600'">
                                        {{ getDaysUntilExpiration(selectedProduct.expiration_date) }} días
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Estado de Caducidad:</span>
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                            getDaysUntilExpiration(selectedProduct.expiration_date) <= 30
                                                ? 'bg-red-50 text-red-700 ring-red-600/20'
                                                : getDaysUntilExpiration(selectedProduct.expiration_date) <= 90
                                                    ? 'bg-yellow-50 text-yellow-700 ring-yellow-600/20'
                                                    : 'bg-green-50 text-green-700 ring-green-600/20'
                                        ]"
                                    >
                                        {{ getDaysUntilExpiration(selectedProduct.expiration_date) <= 30 ? 'Próximo a vencer' : 
                                           getDaysUntilExpiration(selectedProduct.expiration_date) <= 90 ? 'Vigente' : 'En buen estado' }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-sm text-gray-500">
                                Este producto no tiene fecha de caducidad
                            </div>
                        </div>

                        <!-- Imagen del producto -->
                        <div v-if="selectedProduct.image_url">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Imagen</h3>
                            <img 
                                :src="selectedProduct.image_url" 
                                :alt="selectedProduct.name"
                                class="w-full h-48 object-cover rounded-lg border"
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <ActionButton
                        variant="secondary"
                        text="Cerrar"
                        @click="closeDetailsModal"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { PlusIcon, PencilIcon, TrashIcon, ExclamationTriangleIcon, PhotoIcon, EyeIcon, CubeIcon, CheckCircleIcon, CurrencyDollarIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useNotifications } from '@/Composables/useNotifications'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PageHeader from '@/Components/Common/PageHeader.vue'
import DataTable from '@/Components/Common/DataTable.vue'
import ActionButton from '@/Components/Common/ActionButton.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Toggle from '@/Components/Toggle.vue'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    categories: {
        type: Array,
        required: true
    },
    brands: {
        type: Array,
        required: true
    },
    suppliers: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({
            search: ''
        })
    }
})

// Computed properties seguras
const safeProducts = computed(() => {
    if (!props.products || !Array.isArray(props.products)) {
        return [];
    }
    return props.products;
});

const activeProducts = computed(() => {
    return safeProducts.value.filter(product => product.is_active).length;
});

const lowStockProducts = computed(() => {
    return safeProducts.value.filter(product => product.stock <= (product.min_stock || 5)).length;
});

const totalInventoryValue = computed(() => {
    return safeProducts.value.reduce((total, product) => {
        return total + (product.price * product.stock);
    }, 0);
});

// Búsqueda y filtrado
const searchQuery = ref(props.filters.search || '');

const filteredProducts = computed(() => {
    if (!searchQuery.value) {
        return safeProducts.value;
    }
    
    const query = searchQuery.value.toLowerCase();
    return safeProducts.value.filter(product => 
        product.name.toLowerCase().includes(query) ||
        product.sku.toLowerCase().includes(query) ||
        product.category.toLowerCase().includes(query)
    );
});

const handleSearch = () => {
    router.get(
        route('admin.products.index'),
        { search: searchQuery.value },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};

const showModal = ref(false)
const showDeleteModal = ref(false)
const showDetailsModal = ref(false)
const isEditing = ref(false)
const selectedProduct = ref(null)
const imageInput = ref(null)
const imagePreview = ref(null)

const form = useForm({
    name: '',
    description: '',
    category_id: '',
    brand_id: '',
    supplier_id: '',
    sku: '',
    price: '',
    stock: '',
    minimum_stock: 5, // Valor automático fijo
    image: null,
    image_url: '',
    has_expiration: false,
    expiration_date: '',
    is_active: true
})

const deleteForm = useForm({})
const { success, error } = useNotifications()

// Watcher para establecer fecha automática cuando se activa has_expiration
watch(() => form.has_expiration, (newValue) => {
    if (newValue && !form.expiration_date) {
        const defaultExpirationDate = new Date()
        defaultExpirationDate.setMonth(defaultExpirationDate.getMonth() + 18)
        form.expiration_date = defaultExpirationDate.toISOString().split('T')[0]
    } else if (!newValue) {
        // Si se desactiva, limpiar la fecha
        form.expiration_date = ''
    }
})

const handleImageChange = (e) => {
    const file = e.target.files[0]
    if (!file) return

    form.image = file
    const reader = new FileReader()
    reader.onload = (e) => {
        imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const openCreateModal = () => {
    isEditing.value = false
    form.reset()
    form.is_active = true
    form.has_expiration = false
    form.minimum_stock = 5 // Stock mínimo automático
    // Establecer fecha de caducidad por defecto a 18 meses
    const defaultExpirationDate = new Date()
    defaultExpirationDate.setMonth(defaultExpirationDate.getMonth() + 18)
    form.expiration_date = defaultExpirationDate.toISOString().split('T')[0]
    imagePreview.value = null
    showModal.value = true
}

const openEditModal = (product) => {
    isEditing.value = true
    selectedProduct.value = product
    form.name = product.name
    form.description = product.description
    form.category_id = product.category_id
    form.brand_id = product.brand_id
    form.supplier_id = product.supplier_id
    form.sku = product.sku
    form.price = product.price
    form.stock = product.stock
    form.minimum_stock = 5 // Siempre usar 5 como stock mínimo
    form.image_url = product.image_url
    form.is_active = product.is_active
    
    // Manejar fecha de caducidad correctamente
    form.has_expiration = product.has_expiration || false
    if (product.has_expiration && product.expiration_date) {
        // Convertir la fecha al formato correcto para input date (yyyy-mm-dd)
        const date = new Date(product.expiration_date)
        if (!isNaN(date.getTime())) {
            form.expiration_date = date.toISOString().split('T')[0]
        } else {
            form.expiration_date = ''
        }
    } else {
        form.expiration_date = ''
    }
    
    imagePreview.value = null
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    form.reset()
    form.clearErrors()
    imagePreview.value = null
    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

const confirmDelete = (product) => {
    selectedProduct.value = product
    showDeleteModal.value = true
}

const closeDeleteModal = () => {
    showDeleteModal.value = false
    selectedProduct.value = null
}

const submitForm = () => {
    // Asegurar que minimum_stock siempre sea 5
    form.minimum_stock = 5
    
    if (isEditing.value) {
        // Para actualizaciones, usar form.put si no hay imagen nueva, sino form.post con _method
        if (form.image) {
            // Si hay imagen nueva, usar POST con _method
            form.transform((data) => ({
                ...data,
                _method: 'PUT'
            })).post(route('admin.products.update', selectedProduct.value.id), {
                onSuccess: () => {
                    closeModal()
                    success('Producto actualizado', 'El producto se ha actualizado correctamente')
                },
                onError: (errors) => {
                    console.error('Error al actualizar:', errors)
                    error('Error al actualizar', 'No se pudo actualizar el producto')
                },
                preserveScroll: true,
            })
        } else {
            // Si no hay imagen nueva, usar PUT normal
            form.put(route('admin.products.update', selectedProduct.value.id), {
                onSuccess: () => {
                    closeModal()
                    success('Producto actualizado', 'El producto se ha actualizado correctamente')
                },
                onError: (errors) => {
                    console.error('Error al actualizar:', errors)
                    error('Error al actualizar', 'No se pudo actualizar el producto')
                },
                preserveScroll: true,
            })
        }
    } else {
        // Para creación, usar POST normal
        form.post(route('admin.products.store'), {
            onSuccess: () => {
                closeModal()
                success('Producto creado', 'El producto se ha creado correctamente')
            },
            onError: (errors) => {
                console.error('Error al crear:', errors)
                error('Error al crear', 'No se pudo crear el producto')
            },
            preserveScroll: true,
        })
    }
}

const deleteProduct = () => {
    deleteForm.delete(route('admin.products.destroy', selectedProduct.value.id), {
        onSuccess: () => {
            closeDeleteModal()
            success('Producto eliminado', 'El producto se ha eliminado correctamente')
        },
        onError: () => {
            error('Error al eliminar', 'No se pudo eliminar el producto')
        }
    })
}

const getDaysUntilExpiration = (expirationDate) => {
    if (!expirationDate) return 0
    const today = new Date()
    const expDate = new Date(expirationDate)
    const diffTime = expDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
}

const viewProductDetails = (product) => {
    selectedProduct.value = product
    showDetailsModal.value = true
}

const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedProduct.value = null
}
</script> 