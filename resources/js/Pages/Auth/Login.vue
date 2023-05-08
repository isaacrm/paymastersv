<template>
    <Head title="Iniciar Sesión" />
    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Iniciar Sesión
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <q-input filled id="email" type="email" v-model="form.email" label="Email" required autofocus
                    :error-message="form.errors.email" :error="form.errors.hasOwnProperty('email')" />
                <q-input filled id="password" type="password" v-model="form.password" label="Contraseña" required
                    :error-message="form.errors.password" :error="form.errors.hasOwnProperty('password')" />
                <div class="flex items-center justify-end mt-4">
                    <q-btn flat no-caps v-if="canResetPassword" :href="route('password.request')" class="text-primary">
                        ¿Olvidaste tu contraseña?
                    </q-btn>
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit">
                        Ingresar
                    </q-btn>
                </div>
            </q-form>
        </q-card-section>
    </AuthLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

defineProps({
    canResetPassword: Boolean,
});

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.transform(data => ({
        ...data
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
