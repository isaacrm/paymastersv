<template>
    <Head title="Email Verification" />

    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Verificar correo electrónico
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <div class="q-mb-md text-body text-grey-9 text-center">
                Antes de continuar, ¿Podría verificar su dirección de correo electrónico haciendo clic en el enlace que le
                acabamos de enviar? Si no recibió el correo electrónico, con gusto le enviaremos otro.
            </div>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <div class="flex items-center justify-center">
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" type="submit">
                        Reenviar correo de verificación
                    </q-btn>
                    <div>
                        <q-btn flat no-caps :href="route('profile.show')" class="text-primary">
                            Editar perfil
                        </q-btn>
                        <q-btn flat no-caps @click="logout" class="text-primary ml-2">
                            Cerrar sesión
                        </q-btn>
                    </div>
                </div>
            </q-form>
            <div v-if="verificationLinkSent" class="q-mb-md text-body text-green-7 font-medium text-center">
                Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionó en la
                configuración de su perfil.
            </div>
        </q-card-section>
    </AuthLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const logout = () => {
    form.post(route('logout'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>
