<template>
    <q-card class="my-card">
        <q-card-section class="ml-6">
            <div class="text-h6">Información de perfil</div>
            <div class="text-subtitle">Actualiza la información de tu cuenta y correo electrónico.</div>
        </q-card-section>
        <q-card-section>
            <q-form @submit.native.prevent="updateProfileInformation" class="q-gutter-md">
                <q-input filled id="name" type="text" v-model="form.name" label="Nombre" autofocus
                    :error-message="form.errors.name" :error="form.errors.hasOwnProperty('name')" />
                <q-input filled id="email" type="email" v-model="form.email" label="Email"
                    :error-message="form.errors.email" :error="form.errors.hasOwnProperty('email')" />
                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="q-pa-sm text-sm mt-2 text-negative">
                        Tu correo electrónico no está verificado.

                        <q-btn color="secondary" :href="route('verification.send')" method="post"
                            @click.prevent="sendEmailVerification">
                            Click aquí para re-enviar el correo de verificación.
                        </q-btn>
                    </p>
                    <div v-show="verificationLinkSent" class="q-pa-sm text-body text-blue-grey font-medium">
                        Un nuevo enlace de verificación ha sido enviado a su correo electrónico.
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <div v-if="form.recentlySuccessful" class="q-mb-md text-body text-green-7 font-medium">
                        Guardado
                    </div>
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit">
                        Guardar
                    </q-btn>
                </div>
            </q-form>
        </q-card-section>
    </q-card>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

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

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>
