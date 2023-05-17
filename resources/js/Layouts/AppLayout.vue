<template>
    <Head :title="title" />

    <q-layout view="hHh lpR fFf" class="bg-grey-1">
        <q-header elevated class="bg-grey-9 text-white-8 q-py-xs" height-hint="58">
            <q-toolbar class="bg-grey-9 text-white">
                <q-btn flat round dense @click="toggleLeftDrawer" aria-label="Menu" icon="menu" />
                <q-btn flat no-caps no-wrap class="q-ml-xs" v-if="$q.screen.gt.xs">
                    <q-toolbar-title shrink class="text-weight-bold" @click="dashboard()">
                        Paymaster SV
                    </q-toolbar-title>
                </q-btn>
                <q-space />
                <q-btn flat dense no-wrap>
                    <q-icon name="more_vert" />
                    <q-menu auto-close>
                        <q-list dense>
                            <q-item class="GL__menu-link-signed-in">
                                <q-item-section>
                                    <div>Usuario: <strong>{{ $page.props.auth.user.name }}</strong></div>
                                </q-item-section>
                            </q-item>
                            <q-separator />
                            <q-item clickable class="GL__menu-link" @click="perfil()">
                                <q-item-section>Perfil</q-item-section>
                            </q-item>
                            <q-separator />

                            <q-item clickable class="GL__menu-link" @click="logout()">
                                <q-item-section>Cerrar Sesión</q-item-section>
                            </q-item>
                        </q-list>
                    </q-menu>
                </q-btn>
            </q-toolbar>
        </q-header>
        <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="bg-grey-2" :width="240">
            <q-scroll-area class="fit">
                <q-list padding>
                    <q-item v-for="link in inicio" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator class="q-my-md" />

                    <q-item v-for="link in links2" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator class="q-mt-md q-mb-xs" />

                    <q-item-label header class="text-weight-bold text-uppercase">
                        Direcciones
                    </q-item-label>

                    <q-item v-for="link in direcciones" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator class="q-my-md" />

                    <q-item-label header class="text-weight-bold text-uppercase">
                        Configuración
                    </q-item-label>

                    <q-item v-for="link in configuracion" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator class="q-my-md" />

                    <q-item-label header class="text-weight-bold text-uppercase">
                        Registro
                    </q-item-label>

                    <q-item v-for="link in registro" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="grey" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label>{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator class="q-mt-md q-mb-lg" />

                    <div class="q-px-md text-grey-9">
                        <div class="row items-center q-gutter-x-sm q-gutter-y-xs">
                            <a v-for="button in sidebar_footer" :key="button.text" class="YL__drawer-footer-link"
                                href="javascript:void(0)">
                                {{ button.text }}
                            </a>
                        </div>
                    </div>
                    <div class="q-py-md q-px-md text-grey-9">
                        <div class="row items-center q-gutter-x-sm q-gutter-y-xs">
                            <a v-for="button in buttons2" :key="button.text" class="YL__drawer-footer-link"
                                href="javascript:void(0)">
                                {{ button.text }}
                            </a>
                        </div>
                    </div>
                </q-list>
            </q-scroll-area>
        </q-drawer>
        <q-page-container>
            <!-- Page Content -->
            <slot />
        </q-page-container>
    </q-layout>
</template>

<script setup>
/* IMPORTANDO COMPONENTES NECESARIOS */
import { ref } from 'vue'
import { fabYoutube } from '@quasar/extras/fontawesome-v6'
import { Head, router } from '@inertiajs/vue3';

defineProps({
    title: String,
});

/* REDIRECCION */
const logout = () => {
    router.post(route('logout'));
};

const perfil = () => {
    location.href = route('profile.show');
};

const dashboard = () => {
    location.href = route('dashboard');
};

/* Para el menú lateral */
const leftDrawerOpen = ref(false)

function toggleLeftDrawer() {
    leftDrawerOpen.value = !leftDrawerOpen.value
}

// En path va la ruta que se especifica en web.php
const inicio = [
    { icon: 'home', text: 'Dashboard', path: '/dashboard' }
]
const links2 = [
    { icon: 'whatshot', text: 'Trending' },
    { icon: 'subscriptions', text: 'Subscriptions' },
    { icon: 'folder', text: 'Library' },
    { icon: 'restore', text: 'History' },
    { icon: 'watch_later', text: 'Watch later' },
    { icon: 'thumb_up_alt', text: 'Liked videos' }
]
const direcciones = [
    { icon: 'map', text: 'Departamentos' },
    { icon: 'location_city', text: 'Municipios' },
    { icon: 'location_on', text: 'Direcciones' },
    { icon: 'live_tv', text: 'Live' }
]
const configuracion = [
    { icon: 'perm_identity', text: 'Tipo de Documentos', path: '/tipo_documentos' },
    { icon: 'flag', text: 'Report history' },
    { icon: 'help', text: 'Help' },
    { icon: 'feedback', text: 'Send feedback' }
]
const registro = [
    { icon: 'perm_identity', text: 'Empleados' },
    { icon: 'text_snippet', text: 'Ingresos', path: '/ingresos' },
    { icon: 'query_stats', text: 'Descuentos',path: '/descuentos' },
    { icon: 'store', text: 'Empresas',path: '/empresas' }
]
const sidebar_footer = [
    { text: 'About' },
    { text: 'Press' },
    { text: 'Copyright' },
    { text: 'Contact us' },
    { text: 'Creators' },
    { text: 'Advertise' },
    { text: 'Developers' }
]
const buttons2 = [
    { text: 'Terms' },
    { text: 'Privacy' },
    { text: 'Policy & Safety' },
    { text: 'Test new features' }
]

</script>

<style lang="sass">
.YL

  &__toolbar-input-container
    min-width: 100px
    width: 55%

  &__toolbar-input-btn
    border-radius: 0
    border-style: solid
    border-width: 1px 1px 1px 0
    border-color: rgba(0,0,0,.24)
    max-width: 60px
    width: 100%

  &__drawer-footer-link
    color: inherit
    text-decoration: none
    font-weight: 500
    font-size: .75rem

    &:hover
      color: #000
</style>