<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="min-h-screen flex">
        <!-- Lado izquierdo - Imagen y mensaje de bienvenida -->
        <div class="hidden lg:flex lg:w-1/2 bg-gray-800 text-white p-12 flex-col justify-between">
            <div>
                <ApplicationLogo class="w-32" />
                <div class="mt-12">
                    <h2 class="text-4xl font-bold">Bienvenido a Ferrechincha</h2>
                    <p class="mt-4 text-gray-300 text-lg">Sistema de gestión integral para tu ferretería</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>Control de inventario en tiempo real</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>Gestión de ventas y facturación</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-purple-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <p>Administración de clientes</p>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Formulario de login -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm">
                <div class="text-center lg:hidden">
                    <ApplicationLogo class="mx-auto w-20" />
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">Bienvenido</h2>
                </div>

                <div class="mt-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Iniciar Sesión</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            ¿No tienes una cuenta?
                            <Link :href="route('register')" class="font-medium text-blue-600 hover:text-blue-500">
                                Regístrate aquí
                            </Link>
                        </p>
                    </div>

                    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                            <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between">
                                <InputLabel for="password" value="Contraseña" />
                                <Link
                                    :href="route('password.request')"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-500"
                                >
                                    ¿Olvidaste tu contraseña?
                                </Link>
                            </div>
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

                        <div class="mt-6 flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ml-2 text-sm text-gray-600">Recordar sesión</span>
            </div>

                        <div class="mt-6">
                            <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Iniciar Sesión
                </PrimaryButton>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</template>
