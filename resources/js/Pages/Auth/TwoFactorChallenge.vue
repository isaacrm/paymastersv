<template>
    <Head title="Confirmación de dos factores" />
    <AuthLayout>
        <q-card-section>
            <div class="text-center q-pt-lg">
                <div class="col text-h6 ellipsis">
                    Autenticación de dos factores
                </div>
            </div>
        </q-card-section>
        <q-card-section>
            <div class="mb-4 text-center text-grey-7">
                <template v-if="!recovery">
                    Confirme el acceso a su cuenta ingresando el código de autenticación proporcionado por su aplicación de
                    autenticación.
                </template>

                <template v-else>
                    Confirme el acceso a su cuenta ingresando uno de sus códigos de recuperación de emergencia.
                </template>
            </div>
            <q-form @submit.native.prevent="submit" class="q-gutter-md">
                <div v-if="!recovery">
                    <q-input id="code" ref="codeInput" name="code" v-model="form.code" type="text" label="Código de autenticación" filled
                        autofocus :error-message="form.errors.code" :error="form.errors.hasOwnProperty('code')">
                    </q-input>
                </div>
                <div v-else>
                    <q-input id="recovery_code" ref="recoveryCodeInput" name="recovery_code" v-model="form.recovery_code"
                        type="text" label="Código de recuperación" filled autofocus
                        :error-message="form.errors.recovery_code" :error="form.errors.hasOwnProperty('recovery_code')">
                    </q-input>
                </div>
                <div class="flex items-center justify-center">
                    <q-btn color="primary" @click.prevent="toggleRecovery">
                        <template v-if="!recovery">
                            Usar un código de recuperación
                        </template>
                        <template v-else>
                            Usar un código de autenticación
                        </template>
                    </q-btn>
                    <div>
                        <q-btn flat color="primary" :class="{ 'opacity-25': form.processing }" :disable="form.processing">
                            Iniciar Sesión
                        </q-btn>
                    </div>
                </div>
            </q-form>
        </q-card-section>
    </AuthLayout>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>
