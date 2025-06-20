<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registro" />

    <div class="min-h-screen flex">
        <!-- Lado izquierdo - Imagen y mensaje de bienvenida -->
        <div class="hidden lg:flex lg:w-1/2 bg-gray-800 text-white p-12 flex-col justify-between">
            <div>
                <ApplicationLogo class="w-32" />
                <div class="mt-12">
                    <h2 class="text-4xl font-bold">Únete a Ferrechincha</h2>
                    <p class="mt-4 text-gray-300 text-lg">Crea tu cuenta y comienza a gestionar tu negocio</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>Acceso a todas las funcionalidades</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>Soporte técnico incluido</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-purple-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <p>Actualizaciones gratuitas</p>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Formulario de registro -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm">
                <div class="text-center lg:hidden">
                    <ApplicationLogo class="mx-auto w-20" />
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">Registro</h2>
                </div>

                <div class="mt-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Crear cuenta</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            ¿Ya tienes una cuenta?
                            <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500">
                                Inicia sesión aquí
                            </Link>
                        </p>
                    </div>

        <form @submit.prevent="submit">
            <div>
                            <InputLabel for="name" value="Nombre" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

                        <div class="mt-6">
                            <InputLabel for="email" value="Correo electrónico" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

                        <div class="mt-6">
                            <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

                        <div class="mt-6">
                            <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

                        <div class="mt-6">
                            <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Registrarse
                </PrimaryButton>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</template>
