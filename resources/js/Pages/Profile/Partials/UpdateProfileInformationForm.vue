<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const photoPreview = ref(null);
const photoInput = ref(null);

const profilePhotoUrl = computed(() => {
    return user.profile_photo_path 
        ? `/storage/${user.profile_photo_path}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=7F9CF5&background=EBF4FF`;
});

const hasProfilePhoto = computed(() => {
    return user.profile_photo_path !== null;
});

const form = useForm({
    _method: 'PATCH',
    name: user.name,
    email: user.email,
    photo: null,
    _remove_photo: false,
});

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const removePhoto = () => {
    form._remove_photo = true;
    form.photo = null;
    photoPreview.value = null;
    
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form._remove_photo = false;
            window.location.reload();
        },
    });
};

const updateProfileInformation = () => {
    if (photoInput.value?.files[0]) {
        form.photo = photoInput.value.files[0];
    }
    
    form.post(route('profile.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('photo');
            photoPreview.value = null;
            window.location.reload();
        },
    });
};
</script>

<template>
    <form @submit.prevent="updateProfileInformation" class="mt-6 space-y-6" enctype="multipart/form-data">
        <div class="flex items-center gap-8">
            <!-- Foto de perfil -->
            <div class="flex flex-col items-center space-y-2">
                <div class="relative group">
                    <img v-if="!photoPreview && profilePhotoUrl" :src="profilePhotoUrl" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg" />
                    <img v-else-if="photoPreview" :src="photoPreview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg" />
                    <div v-else class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>

                    <!-- Botones de acción -->
                    <div class="absolute -bottom-2 right-0 flex space-x-2">
                        <button
                            type="button"
                            class="rounded-full bg-blue-600 p-2 text-white shadow-lg hover:bg-blue-700 transition-colors duration-200"
                            @click="selectNewPhoto"
                            title="Cambiar foto"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>

                        <button
                            v-if="hasProfilePhoto"
                            type="button"
                            class="rounded-full bg-red-600 p-2 text-white shadow-lg hover:bg-red-700 transition-colors duration-200"
                            @click="removePhoto"
                            title="Eliminar foto"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <span class="text-sm text-gray-600">Haz clic para cambiar la foto</span>
                <input
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="updatePhotoPreview"
                >
            </div>

            <!-- Información del perfil -->
            <div class="flex-1 space-y-6">
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

            <div>
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
            </div>
            </div>

        <div v-if="props.mustVerifyEmail && user.email_verified_at === null" class="mt-2 text-sm">
            <p class="text-gray-800">
                Tu dirección de correo electrónico no está verificada.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                    class="text-blue-600 hover:text-blue-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                    Haz clic aquí para reenviar el correo de verificación
                    </Link>
                </p>

            <div v-show="props.status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                </div>
            </div>

        <div class="flex items-center gap-4 mt-4">
            <PrimaryButton :disabled="form.processing" class="bg-blue-600 hover:bg-blue-700">
                <span v-if="form.processing">Guardando...</span>
                <span v-else>Guardar cambios</span>
            </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                <p v-if="form.recentlySuccessful" class="text-sm text-green-600">¡Cambios guardados correctamente!</p>
                </Transition>
            </div>
        </form>
</template>
