<template>
    <q-card class="my-card">
        <q-card-section class="ml-6">
            <div class="text-h6">Autenticación de dos pasos</div>
            <div class="text-subtitle">Agregue seguridad adicional a su cuenta usando la autenticaciónde dos pasos.</div>
        </q-card-section>
        <q-card-section>
            <h4 v-if="twoFactorEnabled && !confirming" class="text-grey-9">
                Ha habilitado la autenticación de dos factores.
            </h4>

            <h4 v-else-if="twoFactorEnabled && confirming" class="text-grey-9">
                Termine de habilitar la autenticación de dos factores.
            </h4>

            <h4 v-else class="text-grey-9">
                No ha habilitado la autenticación de dos factores.
            </h4>
            <p class="q-mb-sm text-grey-7">
                Cuando la autenticación de dos factores está habilitada, se le solicitará un token seguro y aleatorio
                durante la autenticación. Puede recuperar este token de la aplicación móvil Google Authenticator o Microsoft
                Authenticator.
            </p>
            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="q-mt-md q-max-w-md text-subtitle text-grey-8">
                        <p v-if="confirming" class="q-font-semibold">
                            Para terminar de habilitar la autenticación de dos factores, escanee el siguiente código QR
                            usando la aplicación de autenticación de su teléfono o ingrese la clave de configuración y
                            proporcione el código OTP generado.
                        </p>

                        <p v-else>
                            La autenticación de dos factores ahora está habilitada. Escanee el siguiente código QR usando la
                            aplicación de autenticación de su teléfono o ingrese la clave de configuración.
                        </p>
                    </div>

                    <div class="mt-4" v-html="qrCode" />

                    <div v-if="setupKey" class="q-mt-md q-max-w-md text-subtitle text-grey-8">
                        <p class="q-font-semibold">
                            Setup Key: <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4">
                        <q-input id="code" name="code" v-model="confirmationForm.code" type="text" label="Código" filled
                            autofocus @keyup.enter.native="confirmTwoFactorAuthentication"
                            :error-message="confirmationForm.errors.code"
                            :error="confirmationForm.errors.hasOwnProperty('code')">
                        </q-input>
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && !confirming">
                    <div class="q-max-w-md text-subtitle text-grey-8 q-font-semibold">
                        <p>
                            Guarde estos códigos de recuperación en un administrador de contraseñas seguro. Se pueden
                            utilizar para recuperar el acceso a su cuenta si pierde su dispositivo de autenticación de dos
                            factores.
                        </p>
                    </div>

                    <div
                        class="q-grid q-gap-xs q-max-w-md q-mt-md q-px-md q-py-md q-font-mono text-subtitle2 q-bg-grey-2 q-rounded-lg">
                        <div v-for="code in recoveryCodes" :key="code" class="q-py-xs q-px-sm q-bg-grey-4 q-rounded-md">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="q-mt-md">
                <div v-if="!twoFactorEnabled">
                    <ConfirmarContra @confirmed="enableTwoFactorAuthentication">
                        <q-btn color="primary" :class="{ 'q-opacity-25': enabling }" :disable="enabling">
                            habilitar
                        </q-btn>
                    </ConfirmarContra>
                </div>

                <div v-else>
                    <ConfirmarContra @confirmed="confirmTwoFactorAuthentication">
                        <q-btn v-if="confirming" color="primary" class="q-mr-md" :class="{ 'q-opacity-25': enabling }"
                            :disable="enabling">
                            Confirme
                        </q-btn>
                    </ConfirmarContra>

                    <ConfirmarContra @confirmed="regenerateRecoveryCodes">
                        <q-btn v-if="recoveryCodes.length > 0 && !confirming" color="secondary" class="q-mr-md">
                            Regenerar códigos de recuperación
                        </q-btn>
                    </ConfirmarContra>

                    <ConfirmarContra @confirmed="showRecoveryCodes">
                        <q-btn v-if="recoveryCodes.length === 0 && !confirming" color="secondary" class="q-mr-md">
                            Mostrar códigos de recuperación
                        </q-btn>
                    </ConfirmarContra>

                    <ConfirmarContra @confirmed="disableTwoFactorAuthentication">
                        <q-btn v-if="confirming" color="secondary" :class="{ 'q-opacity-25': disabling }"
                            :disable="disabling">
                            Cancelar
                        </q-btn>
                    </ConfirmarContra>

                    <ConfirmarContra @confirmed="disableTwoFactorAuthentication">
                        <q-btn v-if="!confirming" color="negative" :class="{ 'q-opacity-25': disabling }"
                            :disable="disabling">
                            Deshabilitar
                        </q-btn>
                    </ConfirmarContra>
                </div>
            </div>

        </q-card-section>
    </q-card>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ConfirmarContra from '@/Components/ConfirmarContrasena.vue';
import axios from 'axios';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => !enabling.value && usePage().props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>
