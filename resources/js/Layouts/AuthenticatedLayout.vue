<script setup>
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import NavLink from '@/Components/NavLink.vue';
import NotificationContainer from '@/Components/NotificationContainer.vue';
import { useNotifications } from '@/Composables/useNotifications';

const showingNavigationDropdown = ref(false);
const page = usePage();
const { success, error, warning, info } = useNotifications();

const hasRole = (role) => {
    // Verificar que tengamos los datos necesarios
    if (!page.props?.auth?.user) {
        return false;
    }
    
    const user = page.props.auth.user;
    
    // Verificar que el usuario tenga un rol
    if (!user.role) {
        return false;
    }
    
    return user.role === role;
};

const toggleSidebar = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

const adminLinks = [
    { 
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Categorías',
        route: 'admin.categories.index',
        icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
    },
    {
        name: 'Marcas',
        route: 'admin.brands.index',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
    },
    {
        name: 'Proveedores',
        route: 'admin.suppliers.index',
        icon: 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'
    },
    {
        name: 'Productos',
        route: 'admin.products.index',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
    },
    {
        name: 'Clientes',
        route: 'admin.clients.index',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
    },
    {
        name: 'Ventas',
        route: 'admin.sales.index',
        icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'
    },
    {
        name: 'Usuarios',
        route: 'admin.users.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'
    }
];

const employeeLinks = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
    },
    {
        name: 'Productos',
        route: 'employee.products.index',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
    },
    {
        name: 'Ventas',
        route: 'employee.sales.index',
        icon: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'
    }
];

// Manejar notificaciones flash del servidor
watch(() => page.props.flash, (flash) => {
    if (flash?.message) {
        const type = flash.type || 'success';
        
        switch (type) {
            case 'success':
                success(flash.message);
                break;
            case 'error':
                error(flash.message);
                break;
            case 'warning':
                warning(flash.message);
                break;
            case 'info':
                info(flash.message);
                break;
            default:
                success(flash.message);
        }
    }
}, { immediate: true, deep: true });
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Barra lateral -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transition-transform duration-300 ease-in-out lg:translate-x-0',
            showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
        ]">
            <!-- Logo -->
            <div class="h-16 bg-indigo-600">
                <Link :href="route('dashboard')" class="flex h-full items-center px-6">
                    <ApplicationLogo class="block h-8 w-auto text-white" />
                </Link>
            </div>

            <!-- Navegación -->
            <nav class="mt-2">
                <!-- Admin Links -->
                <template v-if="hasRole('admin')">
                    <div class="px-3 py-2">
                        <h2 class="mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Administración
                        </h2>
                        <div class="space-y-1">
                            <Link
                                v-for="(item, index) in adminLinks"
                                :key="index"
                                :href="route(item.route)"
                                class="flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors duration-150 ease-in-out"
                                :class="[
                                    route().current(item.route + '*')
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <svg
                                    class="mr-3 h-5 w-5"
                                    :class="[
                                        route().current(item.route + '*')
                                            ? 'text-indigo-500'
                                            : 'text-gray-400 group-hover:text-gray-500'
                                    ]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="item.icon"
                                    />
                        </svg>
                                {{ item.name }}
                    </Link>
                        </div>
                    </div>
                </template>

                <!-- Employee Links -->
                <template v-if="hasRole('employee')">
                    <div class="px-3 py-2">
                        <h2 class="mb-2 px-4 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Empleado
                        </h2>
                        <div class="space-y-1">
                            <Link 
                                v-for="(item, index) in employeeLinks"
                                :key="index"
                                :href="route(item.route)"
                                class="flex items-center rounded-lg px-4 py-2 text-sm font-medium transition-colors duration-150 ease-in-out"
                                :class="[
                                    route().current(item.route + '*')
                                        ? 'bg-indigo-50 text-indigo-700'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                ]"
                            >
                                <svg
                                    class="mr-3 h-5 w-5"
                                    :class="[
                                        route().current(item.route + '*')
                                            ? 'text-indigo-500'
                                            : 'text-gray-400 group-hover:text-gray-500'
                                    ]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="item.icon"
                                    />
                        </svg>
                                {{ item.name }}
                    </Link>
                        </div>
                    </div>
                </template>
            </nav>
        </aside>

        <!-- Botón de menú móvil -->
        <div class="fixed top-0 left-0 z-50 lg:hidden">
            <button
                @click="toggleSidebar"
                class="mt-3 ml-3 flex h-10 w-10 items-center justify-center rounded-md bg-indigo-600 text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        v-if="showingNavigationDropdown"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                    <path
                        v-else
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>
        </div>

        <!-- Contenido principal -->
        <main class="lg:pl-64">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-900">
                    <slot name="header" />
                        </h1>
                    </div>
                    
                    <!-- Dropdown de usuario -->
                    <div class="ml-4 flex items-center md:ml-6">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                    <span>{{ $page.props.auth.user.name }}</span>
                                    <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Perfil
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Cerrar Sesión
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Contenido de la página -->
            <div class="py-6">
                <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <slot />
                </div>
            </div>
            </main>
        
        <!-- Contenedor de notificaciones -->
        <NotificationContainer />
    </div>
</template>
