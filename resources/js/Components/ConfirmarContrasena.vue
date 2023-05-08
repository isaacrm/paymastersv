<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>
        <div class="q-pa-md q-gutter-sm">
            <q-dialog v-model="confirmingPassword" hide="closeModal" persistent>
                <q-card>
                    <q-card-section class="text-center">
                        <q-avatar icon="warning" color="red" text-color="white" />
                        <div>
                            {{ title }}
                        </div>

                    </q-card-section>
                    <q-card-section class="text-center">
                        <span class="q-ml-sm">{{ content }}.</span>
                    </q-card-section>
                    <q-card-section class="q-pt-none">
                        <q-input ref="passwordInput" v-model="form.password" type="password" label="Password" filled
                            autofocus @keyup.enter.native="confirmPassword" :error-message="form.error"
                            :error="Object.keys(form.error).length !== 0">
                            <template v-slot:append>
                                <q-icon name="lock" />
                            </template>
                        </q-input>
                    </q-card-section>
                    <q-card-actions align="right">
                        <q-btn flat label="Cancelar" color="primary" @click="closeModal" />
                        <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                            :disable="form.processing" @click="confirmPassword">
                            {{ button }}
                        </q-btn>
                    </q-card-actions>
                </q-card>
            </q-dialog>
        </div>
    </span>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue';
import axios from 'axios';

const emit = defineEmits(['confirmed']);

defineProps({
    title: {
        type: String,
        default: 'Confirmar contraseña',
    },
    content: {
        type: String,
        default: 'Por su seguridad, por favor confirme su contraseña para continuar.',
    },
    button: {
        type: String,
        default: 'Confirm',
    },
});

const confirmingPassword = ref(false);

const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;

            setTimeout(() => passwordInput.value.focus(), 250);
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
        passwordInput.value.focus();
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>
