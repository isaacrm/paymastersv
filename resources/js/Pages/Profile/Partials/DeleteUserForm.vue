<template>
    <q-card class="my-card">
        <q-card-section class="ml-6">
            <div class="text-h6">Eliminar cuenta</div>
            <div class="text-subtitle">Elimina permanentemente su cuenta.</div>
        </q-card-section>
        <q-card-section>
            <div class="text-sm text-grey-7">
                Una vez que se elimine su cuenta, todos sus recursos y datos se eliminarán de forma permanente. Antes de
                eliminar su cuenta, descargue cualquier dato o información que desee conservar.
            </div>

            <div class="flex items-center justify-end mt-2">
                <q-btn color="negative" @click="confirmUserDeletion">
                    Eliminar cuenta
                </q-btn>
            </div>
        </q-card-section>
    </q-card>
    <div class="q-pa-md q-gutter-sm">
        <q-dialog v-model="confirmingUserDeletion" hide="closeModal" persistent>
            <q-card>
                <q-card-section class="text-center">
                    <q-avatar icon="warning" color="red" text-color="white" />
                </q-card-section>
                <q-card-section class="text-center">
                    <span class="q-ml-sm">¿Está seguro de que desea eliminar su cuenta? Una vez que se elimine su cuenta,
                        todos sus recursos y datos se eliminarán de forma permanente. Ingrese su contraseña para confirmar
                        que desea eliminar su cuenta de forma permanente.</span>
                </q-card-section>
                <q-card-section class="q-pt-none">
                    <q-input ref="passwordInput" v-model="form.password" type="password" label="Password" filled autofocus
                        @keyup.enter.native="deleteUser" :error-message="form.errors.password"
                        :error="form.errors.hasOwnProperty('password')">
                        <template v-slot:append>
                            <q-icon name="lock" />
                        </template>
                    </q-input>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Cancelar" color="primary" @click="closeModal" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>
