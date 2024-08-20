<template>
    <AppLayout title="Dashboard">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Tipo de Documentos</div>
                    <div class="text-subtitle">Registro de los documentos que el empleado puede entregar.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <q-item>
                                <q-input filled bottom-slots v-model="tipoDocumento.nombre" class="full-width"
                                    label="Nombre" :error-message="errores.nombre && errores.nombre[0]"
<<<<<<< HEAD
                                    :error="hayError(errores.nombre)" />
=======
                                    :error="errores.hasOwnProperty('nombre')" autofocus/>
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tipoDocumento.longitud" type="number"
                                    class="full-width" label="Longitud"
                                    :error-message="errores.longitud && errores.longitud[0]"
                                    :error="hayError(errores.longitud)" />
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
                        <q-btn outline rounded color="primary" label="Guardar" icon="add" @click="guardar($page.props.auth.user.id)"></q-btn>
                        <q-btn outline rounded color="danger" label="Cancelar" icon="cancel" @click="cancelar"></q-btn>
                    </div>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <div class="q-gutter-sm">
                            <q-btn round color="warning" icon="edit" class="mr-2" @click="editar(props.row)"></q-btn>
                            <q-btn round color="negative" icon="delete"
                            @click="confirmarEliminar(props.row.id, props.row.nombre)"></q-btn>
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
                        <span class="q-ml-sm">¿Desea eliminar {{ nombreRegistroEliminar }}?.</span>
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn flat label="No" color="primary" v-close-popup />
                        <q-btn flat label="Sí" color="primary" @click="eliminar($page.props.auth.user.id)" v-close-popup />
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
const tipoDocumento = ref({}) // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

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
    { name: 'nombre', align: 'left', label: 'Nombre', field: 'nombre', sortable: true },
    { name: 'longitud', align: 'left', label: 'Longitud máxima', field: 'longitud', sortable: true },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    tipoDocumento.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

<<<<<<< HEAD
// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor)
        return true
    else
        return false
=======
const cancelar = () => {
    tipoDocumento.value = {}

>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
}

// Operacion de guardar
const guardar = async (user_id) => {
    submitted.value = true
    errores.value = {}
    tipoDocumento.value.user_id = user_id;

    // Actualizar
    if (tipoDocumento.value.id) {
        await axios
            .post("/api/actualizar",tipoDocumento.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Tipo de documento guardado.'
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
                        message: 'Error al agregar el tipo de documento.'
                    }
                )
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/agregar", tipoDocumento.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Tipo de documento guardado.'
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
                        message: 'Error al agregar el tipo de documento.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarTipoDocumentos) => {
    tipoDocumento.value = { ...editarTipoDocumentos }
    submitted.value = false;
    errores.value = {}
}

// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
    tipoDocumento.value.id = id
    nombreRegistroEliminar.value = nombre
    confirmarEliminacion.value = true
}


// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async (user_id) => {
    tipoDocumento.value.user_id = user_id;
    await axios
<<<<<<< HEAD
        .post("/api/eliminar/" + tipoDocumento.value.id)
=======
        .post("/api/tipo_documentos/eliminar/" + tipoDocumento.value.id, tipoDocumento.value)
>>>>>>> 67f9b0ba06f01cdfb3b337336af388d03c3085b8
        .then((response) => {
            reiniciarValores()
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'positive',
                    message: 'Tipo de documento eliminado.'
                }
            )

        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'negative',
                    message: 'Error al eliminar el tipo de documento.'
                }
            )
        })
}

/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/tabla_tipo_documentos", {
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
