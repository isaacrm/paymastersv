<template>
    <AppLayout title="Unidades">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">{{ nombres.mayu + "es" }}</div>
                    <div class="text-subtitle">Registro de las {{ nombres.minu + "es" }} de trabajo de la organizacion.
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <q-item>
                                <q-input filled bottom-slots v-model="datos.nombre" class="full-width" label="Nombre:"
                                    :error-message="errores.nombre && errores.nombre[0]"
                                    :error="hayError(errores.nombre)" autofocus/>
                            </q-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select v-model="datosSelect.superior_id" label="Seleccione un superior"
                                    :options="superiores" option-label="name" option-value="id" class="full-width" filled
                                    clearable />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select v-model="datosSelect.centro_de_costos" label="Seleccione un centro de costos"
                                    :options="centro_de_costos" option-label="name" option-value="id" class="full-width"
                                    filled clearable />
                            </q-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <q-item>
                                <q-select v-model="datosSelect.nivel_organizacional"
                                    label="Seleccione el superior en el nivel organizacional" :options="unidades"
                                    option-label="name" option-value="id" class="full-width" filled clearable />
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

const datos = ref({}) // El objeto que se enviara mediante el request
const datosSelect = ref({})
const nombres = {
    minu: "unidad",
    mayu: "Unidad",
}

const superiores = ref([])
const centro_de_costos = ref([])
const unidades = ref([])

const confirmarEliminacion = ref(false)
const nombreRegistroEliminar = ref('')
const errores = ref({})
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    page: 1,
    rowsPerPage: 5,
    rowsNumber: 0
})
// Fin de fijos e imperativos
// Definiendo las columnas que contendra la tabla. Esto es customizable
const columns = [
    { name: 'nombre', align: 'left', label: 'Nombre', field: 'nombre', sortable: true },
    { name: 'superior_nombre', align: 'left', label: 'Superior', field: 'superior_nombre' },
    { name: 'centro_costos_nombre', align: 'left', label: 'Centro de costos', field: 'centro_costos_nombre' },
    { name: 'nivel_organizacional_nombre', align: 'left', label: 'Nivel organizacional', field: 'nivel_organizacional_nombre' },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }

]
/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})
// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    datos.value = {}
    datosSelect.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''
    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    datos.value = {}
    datosSelect.value = {}
}
// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor)
        return true
    else
        return false
}
// Operacion de guardar
const guardar = async (user_id) => {
    submitted.value = true
    errores.value = {}
    datos.value.user_id = user_id;

    // Actualizar
    if (datosSelect.value.centro_de_costos) { datos.value.centro_de_costos = datosSelect.value.centro_de_costos.id }
    if (datosSelect.value.superior_id) { datos.value.superior_id = datosSelect.value.superior_id.id }
    if (datosSelect.value.nivel_organizacional) { datos.value.nivel_organizacional = datosSelect.value.nivel_organizacional.id }

    if (datos.value.id) {
        await axios
            .post(`/api/${nombres.minu}es_actualizar`, datos.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Unidad actualizada.'
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
                        message: 'Error al agregar la unidad.'
                    }
                )
            })
    }
    //Guardar datos
    else {
        await axios.post(`/api/${nombres.minu}es_agregar`, datos.value).then((response) => {
            reiniciarValores()
            $q.notify(
                {
                    type: 'positive',
                    message: 'Unidad guardada.'
                }
            )
        })
            .catch((e) => {
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors
                }
                $q.notify(
                    {
                        type: 'negative',
                        message: 'Error al agregar la unidad.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editardatos) => {
    // datos.value = { ...editardatos }
    datos.value.id = editardatos.id
    datos.value.nombre = editardatos.nombre
    datosSelect.value.superior_id = { id: editardatos.superior_id, name: editardatos.superior_nombre }
    datosSelect.value.centro_de_costos = { id: editardatos.centro_costos_id, name: editardatos.nombre }
    datosSelect.value.nivel_organizacional = { id: editardatos.nivel_organizacional, name: editardatos.nivel_organizacional_nombre }

    submitted.value = false;
    errores.value = {}
}
// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
    datos.value.id = id
    nombreRegistroEliminar.value = nombre
    confirmarEliminacion.value = true
}
// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async (user_id) => {
    datos.value.user_id = user_id;
    await axios.post(`/api/${nombres.minu}es_eliminar/` + datos.value.id, datos.value).then((response) => {
        reiniciarValores()
        $q.notify(
            {
                type: 'positive',
                message: 'Unidad eliminada.'
            }
        )
    })
        .catch((e) => {
            $q.notify(
                {
                    type: 'negative',
                    message: 'Error al eliminar la unidad.'
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
        .get(`/api/${nombres.minu}es`, {
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


    // * Los valores de select para recargarlos
    await obtenerSuperiores()
    await obtenerCentroDeCostos()
    await obtenerUnidades()
}

const obtenerSuperiores = async () => {
    await axios.get('/api/puestos_consultar_superiores',).then(response => {
        superiores.value = response.data.superiores;
    })
}

const obtenerCentroDeCostos = async () => {
    await axios.get('/api/centro_de_costos_consultar_centro_de_costos',).then(response => {
        centro_de_costos.value = response.data.centro_de_costos;
    })
}

const obtenerUnidades = async () => {
    await axios.get('/api/unidades_consultar_unidades',).then(response => {
        unidades.value = response.data;
    })
}
</script>