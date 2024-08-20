<template>
    <AppLayout title="Roles">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Roles</div>
                    <div class="text-subtitle">Registro de los roles a los que el administrador tiene acceso.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <q-item>
                                <q-input filled bottom-slots v-model="roles.name" class="full-width"
                                    label="Nombre" :error-message="errores.name && errores.name[0]"
                                    :error="hayError(errores.name)" autofocus/>
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
                        <div class="q-gutter-sm">
                            <q-btn round color="warning" icon="edit" class="mr-4" @click="editar(props.row)"></q-btn>
                            <q-btn round color="negative" icon="delete"
                                @click="confirmarEliminar(props.row.id, props.row.name)"></q-btn>
                        </div>
                    </q-td>
                </template>
            </q-table>
        </div>

        <div class="q-pa-md q-gutter-sm">
            <q-dialog v-model="confirmarEliminacion" persistent>
                <q-card>
                    <q-card-section class="row items-center">
                        <q-avatar icon="warning" color="red" text-color="white" />
                        <span class="q-ml-sm">¿Desea eliminar el rol {{ nombreRegistroEliminar }}?</span>
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
const roles = ref({}) // El objeto que se enviara mediante el request
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
    { name: 'name', align: 'left', label: 'Nombre', field: 'name', sortable: true },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    roles.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    roles.value = {}
    
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

    // Actualizar
    if (roles.value.id) {
        await axios
            .post("/api/roles/actualizar", roles.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Rol actualizado.'
                    }
                )

            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                    // Mensaje de alerta para error 422 - Datos improsesables
                    $q.notify({
                    type: 'negative',
                    message: 'Error al actualizar el rol.'
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
                    message: 'Error al actualizar el rol.'
                    });
                }
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/roles/agregar", roles.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Rol guardado.'
                    }
                )

            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                    // Mensaje de alerta para error 422 - Datos improsesables
                    $q.notify({
                    type: 'negative',
                    message: 'Error al guardar el rol.'
                    });
                } else if (e.response.status === 409) {
                    // Mensaje de alerta para error 409 - Error de conflicto (por que ya existe el rol)
                    $q.notify({
                    type: 'negative',
                    message: 'El rol ya existe.'
                    });
                } else {
                    // Mensaje de alerta genérico en caso de otros errores
                    $q.notify({
                    type: 'negative',
                    message: 'Error al guardar el rol.'
                    });
                }
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarRoles) => {
    roles.value = { ...editarRoles }
    submitted.value = false;
    errores.value = {}
}

// Para desplegar el modal
const confirmarEliminar = (id, name) => {
    roles.value.id = id
    nombreRegistroEliminar.value = name;
    confirmarEliminacion.value = true
}


// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
    await axios
        .post("/api/roles/eliminar/" + roles.value.id)
        .then((response) => {
            reiniciarValores()
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'positive',
                    message: 'Rol eliminado.'
                }
            )

        })
        .catch((e) => {
            // Mensaje de alerta
            // Si es un error de tipo 422, es decir, contenido inprocesable
            if (e.response.status === 422) {
                    errores.value = e.response.data.errors
                    // Mensaje de alerta para error 422 - Datos improsesables
                    $q.notify({
                    type: 'negative',
                    message: "Error al eliminar el rol."
                    });
                } else if (e.response.status === 409) {
                    // Mensaje de alerta para error 409 - Error de conflicto (por que ya existe el rol)
                    $q.notify({
                    type: 'negative',
                    message: 'El rol ha sido asignado a un usuario previamente.'
                    });
                } else {
                    // Mensaje de alerta genérico en caso de otros errores
                    $q.notify({
                    type: 'negative',
                    message: 'Error al eliminar el rol.'
                    });
                }
        })
}

/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage, sortBy, descending } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/roles/tabla", {
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
