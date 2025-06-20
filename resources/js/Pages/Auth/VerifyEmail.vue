<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <Head title="Autorización de Cuenta" />

    <div class="min-h-screen flex">
        <!-- Lado izquierdo - Imagen y mensaje de bienvenida -->
        <div class="hidden lg:flex lg:w-1/2 bg-gray-800 text-white p-12 flex-col justify-between">
            <div>
                <ApplicationLogo class="w-32" />
                <div class="mt-12">
                    <h2 class="text-4xl font-bold">Autorización pendiente</h2>
                    <p class="mt-4 text-gray-300 text-lg">Tu cuenta está en proceso de autorización por el administrador</p>
                </div>
            </div>
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-blue-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <p>Proceso de verificación seguro</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-2 bg-green-500 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p>Revisión por el administrador</p>
                </div>
            </div>
        </div>

        <!-- Lado derecho - Contenido de verificación -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm">
                <div class="text-center lg:hidden">
                    <ApplicationLogo class="mx-auto w-20" />
                    <h2 class="mt-6 text-3xl font-bold text-gray-900">Autorización pendiente</h2>
                </div>

                <div class="mt-8">
                    <div class="mb-4 text-sm text-gray-600">
                        ¡Gracias por registrarte en Ferrechincha! Por motivos de seguridad, tu cuenta necesita ser autorizada 
                        por el administrador del sistema. Al hacer clic en el botón de abajo, se enviará una notificación 
                        al administrador para que revise y autorice tu cuenta. Te notificaremos por correo electrónico cuando 
                        tu cuenta haya sido autorizada.
                    </div>

                    <div v-if="verificationLinkSent" class="mb-4 text-sm font-medium text-green-600">
                        Se ha enviado la solicitud de autorización al administrador. Te notificaremos cuando tu cuenta sea activada.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Enviar enlace de autorización
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                                Cerrar sesión
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
