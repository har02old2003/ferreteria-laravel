<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
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
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Contraseña" />

    <div class="min-h-screen flex">
        <!-- Lado izquierdo - Imagen y mensaje de bienvenida -->
        <div class="hidden lg:flex lg:w-1/2 bg-gray-800 text-white p-12 flex-col justify-between">
            <div>
                <ApplicationLogo class="w-32" />
                <div class="mt-12">
                    <h2 class="text-4xl font-bold">¿Olvidaste tu contraseña?</h2>
                    <p class="mt-4 text-gray-300 text-lg">No te preocupes, te ayudamos a recuperar tu acceso</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p>Recibirás un correo con instrucciones</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <p>Proceso seguro y rápido</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-purple-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <p>Crea una nueva contraseña segura</p>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Formulario de recuperación -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm">
                <div class="text-center lg:hidden">
                    <ApplicationLogo class="mx-auto w-20" />
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">Recuperar Contraseña</h2>
                </div>

                <div class="mt-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Restablecer Contraseña</h2>
                        <p class="mt-2 text-sm text-gray-600">
                            ¿Recordaste tu contraseña?
                            <Link :href="route('login')" class="font-medium text-blue-600 hover:text-blue-500">
                                Volver al inicio de sesión
                            </Link>
                        </p>
                    </div>

                    <div class="mb-4 text-sm text-gray-600">
                        ¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña y podrás elegir una nueva.
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
                            <PrimaryButton class="w-full justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enviar enlace de recuperación
                </PrimaryButton>
            </div>
        </form>
                </div>
            </div>
        </div>
    </div>
</template>
