<template>
    <AppLayout title="Asignar permisos">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Asignar Permisos</div>
                    <div class="text-subtitle">Asignación de permisos a roles.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="roles.role_name" class="full-width"
                                    readonly
                                    label="Nombre" :error-message="errores.role_name && errores.role_name[0]"
                                    :error="hayError(errores.role_name)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select filled v-model="selectedPermissions" class="full-width"
                                    multiple
                                    :options="permisos"
                                    emit-value
                                    map-options
                                    option-label="name"
                                    option-value="id"
                                    stack-label
                                    clearable
                                    :disable="!editing"
                                    label="Permisos" :error-message="errores.selectedPermissions && errores.selectedPermissions[0]"
                                    :error="hayError(errores.selectedPermissions)"
                                    transition-show="scale"
                                    transition-hide="scale"
                                    />
                            </q-item>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <div class="q-pa-md">
            <q-table flat bordered :rows="detalleTabla" :columns="columns" row-key="id" v-model:pagination="pagination"
                :loading="loading" :filter="filter" binary-state-sort :rows-per-page-options="[5, 10, 20, 40, 0]"
                @request="generarTabla">
                <template v-slot:top-right>
                    <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <template v-slot:top-left>
                    <div class="q-gutter-sm">
                        <q-btn outline rounded color="primary" label="Guardar" icon="add" @click="guardar"></q-btn>
                        <q-btn outline rounded color="danger" label="Cancelar" icon="cancel" @click="cancelar"></q-btn>
                    </div>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn round color="positive" icon="key" class="mr-2" @click="editar(props.row)"></q-btn>
                    </q-td>
                </template>
            </q-table>
        </div>
        
    </AppLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useQuasar } from 'quasar'
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar() // Para mensajes de exito o error

// De la vista
const detalleTabla = ref([])
const submitted = ref(false) // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false)

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

const permisos = ref([])
const roles = ref({}) // El objeto que se enviara mediante el request para cargar roles
const selectedPermissions = ref([])
const editing = ref(false);


// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    page: 1,
    rowsPerPage: 5,
    /* Cuando se usa server side pagination, QTable necesita
    conocer el "rowsNumber" (Numero total de filas).
    Por qué?
    Porque Quasar no tiene forma de saber cuál será
    la última página sin esta información!
    Por lo tanto, ahora debemos proporcionarle un "número de filas" nosotros mismos.. */
    rowsNumber: 0
})
// Fin de fijos e imperativos

// Definiendo las columnas que contendra la tabla. Esto es customizable
const columns = [
  { name: 'role_name', align: 'left', label: 'Rol', field: 'role_name', sortable: false },
  { 
    name: 'permissions', 
    align: 'left', 
    label: 'Permisos', 
    field: 'permissions', 
    sortable: false, 
    format: (value) => value.map((permiso) => permiso.name).join(', '),
    
  },
  { name: 'operaciones', align: 'center', label: 'Asignación de Permisos'}
];


/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

onMounted(async() =>{
    try {
        const response = await axios.get('/api/permisos/select')
        permisos.value = response.data
    } catch (error) {
        console.log(error)
    }
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    roles.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false

    selectedPermissions.value = ''

    editing.value = false; // Establecer editing en true al presionar el botón de editar

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    roles.value = {}
    selectedPermissions.value = ''
    editing.value = false; // Establecer editing en true al presionar el botón de editar

}

// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor)
        return true
    else
        return false
}

// Operacion de guardar
const guardar = async () => {
    submitted.value = true
    errores.value = {}

    roles.value.permissions=selectedPermissions.value

    console.log(roles.value)

    if (selectedPermissions.value.length === 0) {
        errores.value.selectedPermissions = ['El campo permisos es requerido'];
      return; // Detener la ejecución si no se seleccionó ningún rol
    }

    // Actualizar
    if (roles.value.id) {
        await axios
            .post("/api/roles/permisos/asignar", roles.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Permisos asignados.'
                    }
                )
            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors
                }
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'negative',
                        message: 'Error al actualizar el permiso.'
                    }
                )
            })
    }
}

// Para mostrar los datos en el form
const editar = (editarRoles) => {
    roles.value = { ...editarRoles }
    const permisosSeleccionados = roles.value.permissions;
    selectedPermissions.value = permisosSeleccionados;

    submitted.value = false;
    errores.value = {}

    editing.value = true; // Establecer editing en true al presionar el botón de editar
}

/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/roles/permisos/tabla", {
            params: {
                page,
                rowsPerPage,
                filter
            }
        })
        .then(response => {
            detalleTabla.value = response.data.detalle
            // Actualizando el objeto de paginación local. Solamente se cambia la info del response data de ser necesario
            pagination.value.page = response.data.paginacion.pagina
            pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina
            pagination.value.rowsNumber = response.data.paginacion.tuplas

        })
        .catch(error => {
            errored.value = true
        })

    // Apagando el indicador de carga. Este no se toca
    loading.value = false
}

</script>
