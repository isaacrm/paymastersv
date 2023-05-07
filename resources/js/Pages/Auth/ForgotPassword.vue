<template>
    <Head title="Contraseña Olvidada" />
    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Contraseña Olvidada
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <div class="q-mb-md text-body text-grey-9 text-center">
                ¿Olvidaste tu contraseña? No hay problema. Sólo escriba el correo con el que se registró y le enviaremos por
                correo electrónico un enlace de restablecimiento de contraseña que le permitirá elegir una nueva.
            </div>
            <template v-if="status">
                <div class="q-mb-md text-body text-green-7 font-medium">
                    {{ status }}
                </div>
            </template>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <q-input filled id="email" type="email" v-model="form.email" label="Email" required
                    :error-message="form.errors.email" :error="form.errors.hasOwnProperty('email')" autofocus />
                <div class="flex items-center justify-center">
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit" no-caps>
                        Enviar enlace de restablecimiento de contraseña
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
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>
