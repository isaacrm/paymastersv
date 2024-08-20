<template>
    <AppLayout title="Renta mensual">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Tabla de renta mensual</div>
                    <div class="text-subtitle">Según la información proporcionada por el Ministerio de Hacienda de El
                        Salvador.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.tramo" type="number" class="full-width"
                                    label="Tramo" :error-message="errores.tramo && errores.tramo[0]"
                                    :error="errores.hasOwnProperty('tramo')" autofocus />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.desde" type="number" class="full-width"
                                    label="Desde (USD$)" :error-message="errores.desde && errores.desde[0]"
                                    :error="errores.hasOwnProperty('desde')" prefix="$"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.hasta" type="number" class="full-width"
                                    label="Hasta (USD$)" :error-message="errores.hasta && errores.hasta[0]"
                                    :error="errores.hasOwnProperty('hasta')" prefix="$" />
                            </q-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.porcentaje_aplicar" suffix="%" type="number"
                                    class="full-width" label="Porcentaje a aplicar (%)"
                                    :error-message="errores.porcentaje_aplicar && errores.porcentaje_aplicar[0]"
                                    :error="errores.hasOwnProperty('porcentaje_aplicar')" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.sobre_exceso" type="number"
                                    class="full-width" label="Sobre el exceso de"
                                    :error-message="errores.sobre_exceso && errores.sobre_exceso[0]"
                                    :error="errores.hasOwnProperty('sobre_exceso')" prefix="$"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="tramoRenta.mas_fija" type="number" class="full-width"
                                    label="Más cuota fija de" :error-message="errores.mas_fija && errores.mas_fija[0]"
                                    :error="errores.hasOwnProperty('mas_fija')" prefix="$"/>
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
                            @click="confirmarEliminar(props.row.id, props.row.tramo)"></q-btn>
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
                        <span class="q-ml-sm">¿Desea eliminar el Tramo {{ nombreRegistroEliminar }}?.</span>
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
const tramoRenta = ref({}) // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'tramo', // Se actualiza segun columna de ordenamiento por defecto
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

// Para mostrar en la tabla con dos decimales en valores monetarios
const formatoDinero = (valor) => {
    return valor.toFixed(2);
};

// Definiendo las columnas que contendra la tabla. Esto es customizable
// name es importante porque mediante ello se hacen los ordenamientos por esa columna
// field es importante porque es eso lo que permite mostrar los datos en la tabla
const columns = [
    { name: 'tramo', align: 'left', label: 'Tramo', field: 'tramo', sortable: true },
    { name: 'desde', align: 'left', label: 'Desde', field: 'desde', sortable: true, format: formatoDinero },
    { name: 'hasta', align: 'left', label: 'Hasta', field: 'hasta', sortable: true, format: formatoDinero },
    { name: 'porcentaje_aplicar', align: 'left', label: 'Porcentaje a aplicar (%)', field: 'porcentaje', sortable: true },
    { name: 'sobre_exceso', align: 'left', label: 'Sobre el exceso de', field: 'sobre_exceso', sortable: true, format: formatoDinero },
    { name: 'mas_fija', align: 'left', label: 'Más cuota fija de', field: 'mas_fija', sortable: true, format: formatoDinero },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    tramoRenta.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    tramoRenta.value = {}
    
}

// Operacion de guardar
const guardar = async (user_id) => {
    submitted.value = true
    errores.value = {}
    tramoRenta.value.user_id = user_id;

    // Actualizar
    if (tramoRenta.value.id) {
        await axios
            .post("/api/renta_mensual/actualizar", tramoRenta.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Tramo de renta mensual guardado.'
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
                        message: 'Error al agregar el tramo de renta.'
                    }
                )
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/renta_mensual/agregar", tramoRenta.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Tramo de renta mensual guardado.'
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
                        message: 'Error al agregar el tramo de renta.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarTramoRenta) => {
    tramoRenta.value = { ...editarTramoRenta }
    tramoRenta.value.porcentaje_aplicar *= 100; // Para que el porcentaje salga en la forma amigable con el usuario
    submitted.value = false;
    errores.value = {}
}

// Para desplegar el modal
const confirmarEliminar = (id, tramo) => {
    tramoRenta.value.id = id
    nombreRegistroEliminar.value = tramo
    confirmarEliminacion.value = true
}

// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async (user_id) => {
    tramoRenta.value.user_id = user_id;
    await axios
        .post("/api/renta_mensual/eliminar/" + tramoRenta.value.id, tramoRenta.value)
        .then((response) => {
            reiniciarValores()
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'positive',
                    message: 'Tramo de renta mensual eliminado.'
                }
            )

        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'negative',
                    message: 'Error al eliminar el tramo de renta.'
                }
            )
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
        .get("/api/renta_mensual/tabla", {
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
