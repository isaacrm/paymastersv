<template>
    <q-card class="my-card">
        <q-card-section class="ml-6">
            <div class="text-h6">Actualizar contraseña</div>
            <div class="text-subtitle">Asegúrese de que su cuenta esté usando una contraseña larga y aleatoria para
                mantenerse seguro.</div>
        </q-card-section>
        <q-card-section>
            <q-form @submit.native.prevent="updatePassword" class="q-gutter-md">
                <q-input filled id="current_password" type="password" v-model="form.current_password"
                    label="Contraseña actual" :error-message="form.errors.current_password"
                    :error="form.errors.hasOwnProperty('current_password')" />
                <q-input filled id="password" type="password" v-model="form.password" label="Contraseña"
                    :error-message="form.errors.password" :error="form.errors.hasOwnProperty('password')" />
                <q-input filled id="password_confirmation" type="password" v-model="form.password_confirmation"
                    label="Confirmar contraseña" :error-message="form.errors.password_confirmation"
                    :error="form.errors.hasOwnProperty('password_confirmation')" />
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
import { useForm } from '@inertiajs/vue3';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>
