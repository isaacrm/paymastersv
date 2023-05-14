<template>
    <AppLayout title="Direcciones">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Direcciones</div>
                    <div class="text-subtitle">Registro de los direcciones que el empleado puede agregar.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="direccion.calle" class="full-width"
                                    label="Calle" :error-message="errores.calle && errores.calle[0]"
                                    :error="hayError(errores.calle)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="direccion.colonia"
                                    class="full-width" label="Colonia"
                                    :error-message="errores.colonia && errores.colonia[0]"
                                    :error="hayError(errores.colonia)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="direccion.identificador_casa"
                                    class="full-width" label="Identificador de casa"
                                    :error-message="errores.identificador_casa && errores.identificador_casa[0]"
                                    :error="hayError(errores.identificador_casa)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="direccion.apto_local" type="number"
                                    class="full-width" label="Número de Apartamento o Local"
                                    :error-message="errores.apto_local && errores.apto_local[0]"
                                    :error="hayError(errores.apto_local)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select v-model="selectedDepartamento" class="full-width"
                                    :options="departamentos"
                                    label="Departamento"
                                    emit-value
                                    map-options
                                    option-label="nombre"
                                    option-value="id" 
                                    @update:model-value="cargarMunicipios"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select v-model="direccion.municipio_id" class="full-width"
                                    :options="municipios"
                                    label="Municipios"
                                    emit-value
                                    map-options
                                    option-label="nombre"
                                    option-value="id"
                                    :error-message="errores.municipio_id && errores.municipio_id[0]"
                                    :error="hayError(errores.municipio_id)" />
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
                    <q-btn outline rounded color="primary" label="Guardar" icon="add" @click="guardar"></q-btn>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn round color="warning" icon="edit" class="mr-2" @click="editar(props.row)"></q-btn>
                        <q-btn round color="negative" icon="delete"
                            @click="confirmarEliminar(props.row.id, props.row.nombre)"></q-btn>
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
const direccion = ref({}) // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

const municipios = ref({})//Para almacenar el array de los municipios
const departamentos = ref({})//Para almacenar el array de los departamentos
const selectedDepartamento = ref(null)
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
    { name: 'calle', align: 'left', label: 'Calle', field: 'calle', sortable: true },
    { name: 'colonia', align: 'left', label: 'Colonia', field: 'colonia', sortable: true },
    { name: 'identificador_casa', align: 'left', label: 'Identificador de Casa', field: 'identificador_casa', sortable: true },
    { name: 'apto_local', align: 'left', label: 'Apartamento o Local', field: 'apto_local', sortable: true },
    { name: 'nombre_municipio', align: 'left', label: 'Municipio', field: 'nombre_municipio', sortable: true },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

onMounted(async() =>{
    try {
        const response = await axios.get('/api/data_departamentos');
        departamentos.value = response.data;
    } catch (error) {
        console.log(error);
    }
});


const cargarMunicipios = async () => {
    if (selectedDepartamento.value){
        try {
            const response = await axios.get('/api/data_municipios/'+ selectedDepartamento.value);
            municipios.value = response.data
        } catch (error) {
            console.log(error)
        }
    }
}

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    direccion.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

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

    // Actualizar
    if (direccion.value.id) {
        await axios
            .post("/api/actualizar_direccion",direccion.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Dirección actualizada.'
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
                        message: 'Error al actualizar la dirección.'
                    }
                )
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/agregar_direccion", direccion.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Dirección guardada.'
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
                        message: 'Error al agregar la dirección.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarDirecciones) => {
    direccion.value = { ...editarDirecciones }
    submitted.value = false;
    errores.value = {}
}

// Para desplegar el modal
const confirmarEliminar = (id, calle) => {
    direccion.value.id = id
    nombreRegistroEliminar.value = calle
    confirmarEliminacion.value = true
}


// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
    await axios
        .post("/api/eliminar_direccion/" + direccion.value.id)
        .then((response) => {
            reiniciarValores()
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'positive',
                    message: 'Dirección eliminada.'
                }
            )

        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'negative',
                    message: 'Error al eliminar la dirección.'
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
        .get("/api/tabla_direcciones", {
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
