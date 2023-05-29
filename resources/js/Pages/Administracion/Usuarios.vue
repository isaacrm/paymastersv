<template>
    <AppLayout title="Usuarios">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Usuarios</div>
                    <div class="text-subtitle">Registro de los usuarios existentes en el sistema a los que el administrador tiene acceso.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="usuarios.user_name" class="full-width"
                                    readonly
                                    label="Usuario"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select filled v-model="selectedRoles" class="full-width"
                                    multiple
                                    :options="roles"
                                    emit-value
                                    map-options
                                    option-label="name"
                                    option-value="id"
                                    stack-label
                                    clearable
                                    :rules="[val => !!val || 'Rol es requerido']"
                                    label="Roles" :error-message="errores.selectedRoles && errores.selectedRoles[0]"
                                    :error="hayError(errores.selectedRoles)"
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
                    <q-btn outline rounded color="primary" label="Asignar Rol" icon="add" @click="guardar"></q-btn>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn round color="positive" icon="manage_accounts" class="mr-2" @click="editar(props.row)"></q-btn>
                    </q-td>
                </template>
            </q-table>
        </div>

        <div class="q-pa-md q-gutter-sm">
            <q-dialog v-model="confirmarEliminacion" persistent>
                <q-card>
                    <q-card-section class="row items-center">
                        <q-avatar icon="warning" color="red" text-color="white" />
                        <span class="q-ml-sm">¿Desea eliminar el permiso {{ nombreRegistroEliminar }}?</span>
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn flat label="No" color="primary" v-close-popup />
                        <q-btn flat label="Sí" color="primary" @click="eliminar" v-close-popup />
                    </q-card-actions>
                </q-card>
            </q-dialog>
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
const detalleTabla = ref()
const submitted = ref(false) // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false)

const roles = ref([])
const usuarios = ref({}) // El objeto que se enviara mediante el request
const selectedRoles = ref([])

const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'name', // Se actualiza segun columna de ordenamiento por defecto
    descending: false, // true para descendente (mayor a menor) false para ascendente (menor a mayor)
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
    { name: 'user_name', align: 'left', label: 'Usuario', field: 'user_name', sortable: false },
    { name: 'email', align: 'left', label: 'Correo', field: 'email', sortable: false },
    { 
        name: 'roles', 
        align: 'left', 
        label: 'Roles', 
        field: 'roles', 
        sortable: false, 
        format: (value) => value.map((rol) => rol.name).join(', ')
    },
    { name: 'operaciones', align: 'center', label: 'Asignación de Roles' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

onMounted(async() =>{
    try {
        const response = await axios.get('/api/roles/select')
        roles.value = response.data
    } catch (error) {
        console.log(error)
    }
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    usuarios.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    selectedRoles.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
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

    usuarios.value.roles=selectedRoles.value

    // Actualizar
    if (usuarios.value.id) {
        await axios
            .post("/api/usuarios/roles/asignar", usuarios.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Roles asignados.'
                    }
                )

            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors
                    // Mensaje de alerta para error 422 - Datos improsesables
                    $q.notify({
                    type: 'negative',
                    message: "Error al actualizar el usuario."
                    });
                } else if (e.response.status === 409) {
                    // Mensaje de alerta para error 409 - Error de conflicto (por que ya existe el rol)
                    $q.notify({
                    type: 'negative',
                    message: 'El nombre del rol ya existe.'
                    });
                } else {
                    // Mensaje de alerta genérico en caso de otros errores
                    $q.notify({
                    type: 'negative',
                    message: 'Error al actualizar el usuario.'
                    });
                }
            })
    }
}

// Para mostrar los datos en el form
const editar = (editarUsuarioss) => {
    usuarios.value = { ...editarUsuarioss }
    const rolesSeleccionados = usuarios.value.roles;
    selectedRoles.value = rolesSeleccionados;

    submitted.value = false;
    errores.value = {}
}

/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/usuarios/tabla", {
            params: {
                page,
                rowsPerPage,
                filter,
                sortBy,
                descending: descending ? 0 : 1
            }
        })
        .then(response => {
            detalleTabla.value = response.data.detalle
            // Actualizando el objeto de paginación local. Solamente se cambia la info del response data de ser necesario
            pagination.value.page = response.data.paginacion.pagina
            pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina
            pagination.value.rowsNumber = response.data.paginacion.tuplas
            pagination.value.sortBy = response.data.paginacion.ordenarPor
            pagination.value.descending = descending

        })
        .catch(error => {
            errored.value = true
        })

    // Apagando el indicador de carga. Este no se toca
    loading.value = false
}

</script>
