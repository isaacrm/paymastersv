<template>
    <AppLayout title="Departamentos">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Bitacora</div>
                    <div class="text-subtitle">Registro de los departamentos que el empleado puede agregar a su dirección.</div>
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
const detalleTabla = ref()
const submitted = ref(false) // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false)
const departamento = ref({}) // El objeto que se enviara mediante el request
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
    { name: 'description', align: 'left', label: 'Nombre de Actividad', field: 'description', sortable: true },
    { name: 'causer_id', align: 'left', label: 'causer_id', field: 'causer_name', sortable: true },
    { name: 'properties', align: 'left', label: 'properties', field: 'detalle.properties.atributo', sortable: true },
    { name: 'created_at', align: 'left', label: 'created_at', field: 'created_at', sortable: true },
    { name: 'updated_at', align: 'left', label: 'updated_at', field: 'updated_at', sortable: true }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})


/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage } = props.pagination
    const filter = props.filter
    loading.value = true
    // Obteniendo la tabla de datos
    await axios
        .get("/api/bitacora/tabla", {
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
