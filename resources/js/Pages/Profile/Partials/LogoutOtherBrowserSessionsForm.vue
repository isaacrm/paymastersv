<template>
    <q-card class="my-card">
        <q-card-section class="ml-6">
            <div class="text-h6">Sesiones abiertas</div>
            <div class="text-subtitle">Administra y cierra sesiones activas en otros navegadores y dispositivos.</div>
        </q-card-section>
        <q-card-section>
            <div class="text-sm text-grey-7 mt-4">
                Si es necesario, puede cerrar sesión en todas sus otras sesiones de navegador en todos sus dispositivos.
                Algunas de sus sesiones recientes se enumeran a continuación; sin embargo, esta lista puede no ser
                exhaustiva. Si cree que su cuenta se ha visto comprometida, también debe actualizar su contraseña.
            </div>
            <div v-if="sessions.length > 0" class="q-mt-md q-space-y-md">
                <div v-for="(session, i) in sessions" :key="i" class="q-flex q-items-center">
                    <div>
                        <q-icon v-if="session.agent.is_desktop" name="computer" size="48px" color="grey-5" />
                        <q-icon v-else name="smartphone" size="48px" color="grey-5" />
                    </div>

                    <div class="ml-3">
                        <div class="text-body text-grey-6">
                            {{ session.agent.platform ? session.agent.platform : 'Unknown' }} - {{ session.agent.browser ?
                                session.agent.browser : 'Unknown' }}
                        </div>

                        <div>
                            <div class="text-caption text-grey-7">
                                {{ session.ip_address }},

                                <span v-if="session.is_current_device" class="text-green-8 font-semibold">Este
                                    dispositivo</span>
                                <span v-else>Última vez activo {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end mt-2">
                <q-btn color="primary" @click="confirmLogout">
                    Cerrar todas las demás sesiones.
                </q-btn>
                <div v-if="form.recentlySuccessful" class="q-mb-md text-body text-green-7 font-medium">
                    Hecho
                </div>
            </div>
        </q-card-section>
    </q-card>
    <div class="q-pa-md q-gutter-sm">
        <q-dialog v-model="confirmingLogout" hide="closeModal" persistent>
            <q-card>
                <q-card-section class="text-center">
                    <q-avatar icon="warning" color="red" text-color="white" />
                    <div>
                        Cerrar la sesión en los demás navegadores
                    </div>

                </q-card-section>
                <q-card-section class="text-center">
                    <span class="q-ml-sm">Ingrese su contraseña para confirmar que desea cerrar sesión en sus otras sesiones
                        de navegador en todos sus dispositivos.</span>
                </q-card-section>
                <q-card-section class="q-pt-none">
                    <q-input ref="passwordInput" v-model="form.password" type="password" label="Password" filled autofocus
                        @keyup.enter.native="logoutOtherBrowserSessions" :error-message="form.errors.password"
                        :error="form.errors.hasOwnProperty('password')">
                        <template v-slot:append>
                            <q-icon name="lock" />
                        </template>
                    </q-input>
                </q-card-section>
                <q-card-actions align="right">
                    <q-btn flat label="Cancelar" color="primary" @click="closeModal" />
                    <q-btn color="primary" class="ml-4" :class="{ 'opacity-25': form.processing }"
                        :disable="form.processing" @click="logoutOtherBrowserSessions">
                        Cerrar las otras sesiones
                    </q-btn>
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>
