<template>
    <Head :title="title" />
    <q-layout view="hHh lpR fFf" class="bg-grey-1">
        <q-header elevated class="bg-gradient-header text-white-8 q-py-xs" height-hint="58">
            <q-toolbar class="bg-gradient-header text-white">
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
        <q-drawer v-model="leftDrawerOpen" show-if-above bordered class="bg-menu" :width="240">
            <q-scroll-area class="fit">
                <q-list padding>
                    <q-item v-if="Inicio($page.props.auth.user.permissions)" v-for="link in inicio" :key="link.text" v-ripple clickable :href="link.path">
                        <q-item-section avatar>
                            <q-icon color="black" :name="link.icon" />
                        </q-item-section>
                        <q-item-section>
                            <q-item-label class="text-weight-bold text-black">{{ link.text }}</q-item-label>
                        </q-item-section>
                        <q-separator class="q-mt-md q-mb-xs" />
                    </q-item>

                    <q-item-section v-if="checkAsigPermiso($page.props.auth.user.permissions)">
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

                    <q-item-section v-if="checkAdministrador($page.props.auth.user.permissions)">
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
                        <q-separator class="q-mt-md q-mb-xs" />
                    </q-item-section>
                    
                    <q-item-label v-if="checkRegistro($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">
                        Registro
                    </q-item-label>
                        <q-item v-if="checkRegMovimientos($page.props.auth.user.permissions)" v-for="link in registro_movimientos" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>

                        <q-item v-if="checkRegEmpresa($page.props.auth.user.permissions)" v-for="link in registro_empresa" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    <q-separator class="q-mt-md q-mb-xs" />
                    
                    <q-item-label v-if="checkEmpLabel($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">
                        Empleados
                    </q-item-label>
                        <q-item v-if="checkInfoEmpleados($page.props.auth.user.permissions)" v-for="link in empleados" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item v-if="checkEmpleados($page.props.auth.user.permissions)" v-for="link in empleados_datos" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>

                    <q-item-section v-if="checkDireccion($page.props.auth.user.permissions)">
                        <q-separator class="q-mt-md q-mb-xs" />

                        <q-item-label header class="text-weight-bold text-uppercase text-header-menu">
                            Direcciones
                        </q-item-label>

                        <q-item v-for="link in direcciones" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </q-item-section>

                    <q-separator class="q-mt-md q-mb-xs" />
                    <q-item-label v-if="checkConfiguracion($page.props.auth.user.permissions)" header class="text-weight-bold text-uppercase text-header-menu">
                            Configuracion
                    </q-item-label>
                        <q-item v-if="checkTipoDoc($page.props.auth.user.permissions)" v-for="link in configuracion" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
                        <q-item v-if="checkConfPla($page.props.auth.user.permissions)" v-for="link in configuracion_planilla" :key="link.text" v-ripple clickable :href="link.path">
                            <q-item-section avatar>
                                <q-icon color="black" :name="link.icon" />
                            </q-item-section>
                            <q-item-section>
                                <q-item-label>{{ link.text }}</q-item-label>
                            </q-item-section>
                        </q-item>
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
import { ref } from "vue";
import { fabYoutube } from "@quasar/extras/fontawesome-v6";
import { Head, router } from "@inertiajs/vue3";

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
    { icon: 'manage_accounts', text: 'Asignación de Roles', path: '/usuarios' }
]

const direcciones = [
    { icon: 'location_on', text: 'Departamentos', path: '/departamentos' },
    { icon: 'location_city', text: 'Municipios', path: '/municipios' },
    { icon: 'home', text: 'Direcciones', path: '/direcciones' }
]
const configuracion = [
    { icon: 'perm_identity', text: 'Tipo de Documentos', path: '/tipo_documentos' },
]

const configuracion_planilla = [
    { icon: 'table_rows', text: 'Tabla de Renta Mensual', path: '/renta_mensual' },
    { icon: 'roofing', text: 'Techo Laboral', path: '/techo_laboral' },
    { icon: 'money', text: 'Tabla de Aguinaldo', path: '/aguinaldo' }
]


const registro_movimientos = [
    { icon: "text_snippet", text: "Ingresos", path: "/ingresos" },
    { icon: "query_stats", text: "Descuentos", path: "/descuentos" },
]

const registro_empresa = [
    { icon: "store", text: "Empresas", path: "/empresas" },
    { icon: "engineering", text: "Puestos", path: "/puestos" },
    { icon: "domain", text: "Unidades", path: "/unidades" },
    { icon: "holiday_village", text: "Centro de costos", path: "/centro_de_costos" },
    { icon: "summarize", text: "Planillas", path: "/planillas" },
]

const empleados = [
    { icon: "transgender", text: "Generos", path: "/generos" },
    { icon: "work", text: "Ocupaciones", path: "/ocupaciones" },
    { icon: "family_restroom", text: "Estados Civiles", path: "/estados_civiles" },
]

const empleados_datos = [
    { icon: "group_add", text: "Empleados", path: "/empleados" },
]
/* VERIFICACIÓN DE ROLES */
// Verificar acceso si es rol Super Administrador con roles y permisos, usuarios
const checkAsigPermiso = (permissions) => {
  if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'roles.permisos') {
        return true;
      }
    }
  }
  return false;
};


// Verificar acceso si es rol Super Administrador con roles y permisos, usuarios
const checkAdministrador = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'roles.usuarios') {
        return true;
      }
    }
  }
  return false;
};

// Verificar acceso si es rol Super Administrador con roles y permisos, usuarios
const checkInfoEmpleados = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'empleados.config') {
        return true;
      }
    }
  }
  return false;
};

const checkRegMovimientos = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'registro.movimientos') {
        return true;
      }
    }
  }
  return false;
};

const checkTipoDoc = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'configuracion.doc') {
        return true;
      }
    }
  }
  return false;
}

const checkRegEmpresa = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'registro.empresa') {
        return true;
      }
    }
  }
  return false;
};

const checkDireccion = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'direccion') {
        return true;
      }
    }
  }
  return false;
};

const checkConfPla = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'configuracion.desc') {
        return true;
      }
    }
  }
  return false;
}


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

const Inicio = ( permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'dashboard') {
        return true;
      }
    }
  }
  return false;   
}

const checkEmpleados = (permissions) => {
    if (permissions && permissions.length) {
    for (let i = 0; i < permissions.length; i++) {
      if (permissions[i] === 'empleados.datos') {
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