<template>
    <Head title="Registrarse" />
    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Registrarse
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <q-input filled id="name" type="text" v-model="form.name" label="Nombre" required autofocus
                    :error-message="form.errors.name" :error="form.errors.hasOwnProperty('name')" />
                <q-input filled id="email" type="email" v-model="form.email" label="Email" required
                    :error-message="form.errors.email" :error="form.errors.hasOwnProperty('email')" />
                <q-input filled id="password" type="password" v-model="form.password" label="Contraseña" required
                    :error-message="form.errors.password" :error="form.errors.hasOwnProperty('password')" />
                <q-input filled id="password_confirmation" type="password" v-model="form.password_confirmation"
                    label="Confirmar contraseña" required :error-message="form.errors.password_confirmation"
                    :error="form.errors.hasOwnProperty('password_confirmation')" />
                <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                    <q-checkbox v-model="form.terms" name="terms" required>
                        <template v-slot:label>
                            <div class="q-mb-sm">
                                <div class="q-flex items-center">
                                    <span class="q-mr-sm">I agree to the</span>
                                    <a target="_blank" :href="route('terms.show')" class="text-primary-8 underline-hover">
                                        Terms of Service
                                    </a>
                                    <span class="q-mx-sm">and</span>
                                    <a target="_blank" :href="route('policy.show')" class="text-primary-8 underline-hover">
                                        Privacy Policy
                                    </a>
                                </div>
                            </div>
                        </template>
                    </q-checkbox>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <q-btn flat no-caps :href="route('login')" class="text-primary">
                        ¿Ya se ha registrado?
                    </q-btn>
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit">
                        Registrar
                    </q-btn>
                </div>
            </q-form>
        </q-card-section>
    </AuthLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
