<template>
    <Head title="Cambiar contraseña" />

    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Cambiar contraseña
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <q-input filled id="email" type="email" v-model="form.email" label="Email" required
                    :error-message="form.errors.email" :error="form.errors.hasOwnProperty('email')" autofocus />
                <q-input filled id="password" type="password" v-model="form.password" label="Contraseña" required
                    :error-message="form.errors.password" :error="form.errors.hasOwnProperty('password')" />
                <q-input filled id="password_confirmation" type="password" v-model="form.password_confirmation"
                    label="Confirmar contraseña" required :error-message="form.errors.password_confirmation"
                    :error="form.errors.hasOwnProperty('password_confirmation')" />
                <div class="flex items-center justify-center">
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit" no-caps>
                        Cambiar contraseña
                    </q-btn>
                </div>
            </q-form>
        </q-card-section>
    </AuthLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
