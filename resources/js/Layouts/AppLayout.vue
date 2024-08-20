<template>
    <Head :title="title" />
    <q-layout view="hHh lpR fFf" class="bg-grey-1">
        <q-header elevated class="bg-gradient-header text-white-8 q-py-xs" height-hint="58">
            <q-toolbar class="bg-gradient-header text-white">
                <q-btn flat round dense @click="toggleLeftDrawer" aria-label="Menu" icon="menu" />
                <q-btn flat no-caps no-wrap class="q-ml-xs" v-if="$q.screen.gt.xs">
                    <q-toolbar-title shrink class="text-weight-bold" @click="dashboard()">
                        Paymaster SV -
                    </q-toolbar-title>
                   <q-item-section>
                        <div class="text-weight-bold">{{ fechaActual }}</div>
                   </q-item-section>
                </q-btn>
                <q-space />
                <q-btn flat dense no-wrap>
                    <q-item-section>
                        <div>Bienvenido <strong>{{ $page.props.auth.user.name }}</strong></div>
                     </q-item-section>
                    <q-icon name="more_vert" />
                    <q-menu auto-close>
                        <q-list dense>
                            <q-item class="GL__menu-link-signed-in">
                                <q-item-section>
                                    <div>Correo: <strong>{{ $page.props.auth.user.email }}</strong></div>
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
        <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="bg-menu" :width="240">
            <q-scroll-area class="fit">
                <q-list padding>
                    <q-item v-if="$page.props.auth.user.permissions.includes('dashboard')" v-for="link in inicio" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="black" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label class="text-weight-bold text-black">{{ link.text }}</q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-item-section v-if="$page.props.auth.user.permissions.includes('roles.usuarios')">
                      <q-separator class="q-mt-md q-mb-xs" />
                        <q-item-label header class="text-weight-bold text-uppercase text-header-menu">
                            Usuarios
                        </q-item-label>
                        <q-item v-for="link in usuarios_roles" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-item-section>

<<<<<<< HEAD
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
                        More from Youtube
=======
                    <q-item-section v-if="$page.props.auth.user.permissions.includes('roles.permisos')">
                      <q-separator class="q-mt-md q-mb-xs" />
                        <q-item-label header class="text-weight-bold text-uppercase text-header-menu">
                            Roles y Permisos
                        </q-item-label>

                        <q-item v-for="link in roles_permisos" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-item-section>

                    <q-item-section v-if="$page.props.auth.user.permissions.includes('roles.permisos')">
                      <q-separator class="q-mt-md q-mb-xs" />
                        <q-item-label header class="text-weight-bold text-uppercase text-header-menu">
                            Auditoria
                        </q-item-label>

                        <q-item v-for="link in bitacora" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-item-section>
                    
                    <q-item-label v-if="checkRegistro($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">  
                      <q-separator class="q-mt-md q-mb-xs" />
                      Registro
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
                    </q-item-label>
                        <q-item v-if="$page.props.auth.user.permissions.includes('registro.movimientos')" v-for="link in registro_movimientos" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>

<<<<<<< HEAD
                    <q-item v-for="link in links3" :key="link.text" v-ripple clickable :href="link.path">
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
=======
                          <q-item v-if="$page.props.auth.user.permissions.includes('registro.empresa')" v-for="link in registro_empresa" :key="link.text" v-ripple clickable :href="link.path">
                              <q-item-section avatar>
                                  <q-icon color="black" :name="link.icon" />
                              </q-item-section>
                              <q-item-section>
                                  <q-item-label>{{ link.text }}</q-item-label>
                              </q-item-section>
                        </q-item>
                    
                    <q-item-label v-if="checkEmpLabel($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">
                      <q-separator class="q-mt-md q-mb-xs" />      
                      Empleados
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
                    </q-item-label>
                        <q-item v-if="$page.props.auth.user.permissions.includes('empleados.config')" v-for="link in empleados" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item v-if="$page.props.auth.user.permissions.includes('empleados.datos')" v-for="link in empleados_datos" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>

                    <q-item-section v-if="$page.props.auth.user.permissions.includes('direccion')">
                      <q-separator class="q-mt-md q-mb-xs" />
                        <q-item-label header class="text-weight-bold text-uppercase text-header-menu">
                            Direcciones
                        </q-item-label>

<<<<<<< HEAD
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
=======
                        <q-item v-for="link in direcciones" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-item-section>

                    <q-item-label v-if="checkConfiguracion($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">
                      <q-separator class="q-mt-md q-mb-xs" />      
                      Configuracion
                    </q-item-label>
                        <q-item v-if="$page.props.auth.user.permissions.includes('configuracion.doc')" v-for="link in configuracion" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item v-if="$page.props.auth.user.permissions.includes('configuracion.desc')" v-for="link in configuracion_planilla" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-list>
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
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
import { ref, onMounted } from "vue";
import { fabYoutube } from "@quasar/extras/fontawesome-v6";
import { Head, router } from "@inertiajs/vue3";

const fechaActual = ref('');
onMounted(() => {
  const options = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' };
  const formatter = new Intl.DateTimeFormat('es-ES', options);
  const currentDate = formatter.format(new Date());
  fechaActual.value = currentDate;
});

defineProps({
    title: String,
});

/* REDIRECCION */
const logout = () => {
    router.post(route("logout"));
};

const perfil = () => {
    location.href = route("profile.show");
};

const dashboard = () => {
    location.href = route("dashboard");
};

/* Para el menú lateral */
const leftDrawerOpen = ref(false);

function toggleLeftDrawer() {
    leftDrawerOpen.value = !leftDrawerOpen.value;
}

// En path va la ruta que se especifica en web.php
const inicio = [
    { icon: 'home', text: 'Dashboard', path: '/dashboard' }
]
<<<<<<< HEAD
const links2 = [
    { icon: 'whatshot', text: 'Trending' },
    { icon: 'subscriptions', text: 'Subscriptions' },
    { icon: 'folder', text: 'Library' },
    { icon: 'restore', text: 'History' },
    { icon: 'watch_later', text: 'Watch later' },
    { icon: 'thumb_up_alt', text: 'Liked videos' }
]
const links3 = [
    { icon: fabYoutube, text: 'YouTube Premium' },
    { icon: 'local_movies', text: 'Movies & Shows' },
    { icon: 'videogame_asset', text: 'Gaming' },
    { icon: 'live_tv', text: 'Live' }
]
const configuracion = [
    { icon: 'perm_identity', text: 'Tipo de Documentos', path: '/tipo_documentos' },
    { icon: 'perm_identity', text: 'Generos', path: '/generos' },
    { icon: 'perm_identity', text: 'Ocupaciones', path: '/ocupaciones' },
    { icon: 'perm_identity', text: 'Estados Civiles', path: '/estados_civiles' },
    { icon: 'flag', text: 'Report history' },
    { icon: 'help', text: 'Help' },
    { icon: 'feedback', text: 'Send feedback' }
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
=======
const rosalio = [
    { icon: 'whatshot', text: 'Puestos', path: '/puestos' },
    { icon: 'whatshot', text: 'Unidades', path: '/unidades' },
    { icon: 'whatshot', text: 'Centro de costos', path: '/centro_de_costos' },
]
const roles_permisos = [
    { icon: 'lock', text: 'Roles', path: '/roles' },
    { icon: 'key', text: 'Permisos', path: '/permisos' },
    { icon: 'lock_open', text: 'Asignación de Permisos', path: '/asignacion' }
]

const usuarios_roles = [
    { icon: 'manage_accounts', text: 'Usuarios', path: '/usuarios'},
    { icon: 'admin_panel_settings', text: 'Roles y Estados', path: '/roles_estados' }
]

const bitacora = [
    { icon: 'history', text: 'Bitácora General', path: '/bitacora_general'}
]

const direcciones = [
    { icon: 'location_on', text: 'Departamentos', path: '/departamentos' },
    { icon: 'location_city', text: 'Municipios', path: '/municipios' },
    { icon: 'home', text: 'Direcciones', path: '/direcciones' }
]
const configuracion = [
    { icon: 'person', text: 'Tipo de Documentos', path: '/tipo_documentos' },
]

const configuracion_planilla = [
    { icon: 'table_rows', text: 'Tabla de Renta Mensual', path: '/renta_mensual' },
    { icon: 'roofing', text: 'Techo Laboral', path: '/techo_laboral' },
    { icon: 'money', text: 'Tabla de Aguinaldo', path: '/aguinaldo' }
]


const registro_movimientos = [
    { icon: "text_snippet", text: "Ingresos", path: "/ingresos" },
    { icon: "query_stats", text: "Descuentos", path: "/descuentos" },
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
]

const registro_empresa = [
    { icon: "store", text: "Empresas", path: "/empresas" },
    { icon: "engineering", text: "Puestos", path: "/puestos" },
    { icon: "domain", text: "Unidades", path: "/unidades" },
    { icon: "holiday_village", text: "Centro de costos", path: "/centro_de_costos" },
    { icon: "summarize", text: "Planillas", path: "/planillas" },
]

const empleados = [
    { icon: "transgender", text: "Géneros", path: "/generos" },
    { icon: "work", text: "Ocupaciones", path: "/ocupaciones" },
    { icon: "family_restroom", text: "Estados Civiles", path: "/estados_civiles" },
]

const empleados_datos = [
    { icon: "group_add", text: "Empleados", path: "/empleados" },
]
/* VERIFICACIÓN DE PERMISOS */
const checkConfiguracion = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'configuracion.desc' || permissions[i] === 'configuracion.doc') {
        return true;
      }
    }
  }
  return false;
}

const checkRegistro = ( permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'registro.movimientos' || permissions[i] === 'registro.empresa') {
        return true;
      }
    }
  }
  return false;   
}

const checkEmpLabel = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'empleados.config' || permissions[i] === 'empleados.datos') {
        return true;
      }
    }
  }
  return false; 
}
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
