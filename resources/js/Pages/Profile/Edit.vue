<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi Perfil</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Información del Perfil -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header class="flex items-center mb-6">
                                <div class="p-3 rounded-full bg-blue-100 mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900">Información del Perfil</h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Actualiza la información de tu perfil y tu dirección de correo electrónico.
                                    </p>
                                </div>
                            </header>
                            <UpdateProfileInformationForm />
                        </section>
                    </div>
                </div>

                <!-- Actualizar Contraseña -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header class="flex items-center mb-6">
                                <div class="p-3 rounded-full bg-green-100 mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900">Actualizar Contraseña</h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Asegúrate de usar una contraseña larga y segura para mantener tu cuenta protegida.
                                    </p>
                                </div>
                            </header>
                            <UpdatePasswordForm />
                        </section>
                    </div>
                </div>

                <!-- Sesiones del Navegador -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header class="flex items-center mb-6">
                                <div class="p-3 rounded-full bg-purple-100 mr-4">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900">Sesiones del Navegador</h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Administra y cierra tus sesiones activas en otros navegadores y dispositivos.
                                    </p>
                                </div>
                            </header>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">Este dispositivo</div>
                                            <div class="text-xs text-gray-500">Última actividad: Ahora</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                        Activo
                                    </span>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Eliminar Cuenta -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section>
                            <header class="flex items-center mb-6">
                                <div class="p-3 rounded-full bg-red-100 mr-4">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900">Eliminar Cuenta</h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente.
                                    </p>
                                </div>
                            </header>
                            <div class="flex justify-start">
                                <button class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Eliminar Cuenta
                                </button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
