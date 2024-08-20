<template>
    <AppLayout title="Bitácora general">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Bitácora General</div>
                    <div class="text-subtitle">Registro de los cambios efectuados en el sistema.</div>
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
    { name: 'description', align: 'left', label: 'Actividad', field: 'description', sortable: true },
    { name: 'subject_type', align: 'left', label: 'Tabla', field: 'subject_type', sortable: true, format:(value)=>{
        if(!value){return ""}
        return value.replace(/^App\\Models\\/, '');
    } },
    { name: 'subject_id', align: 'left', label: 'Id', field: 'subject_id', sortable: true},
    { name: 'causer_id', align: 'left', label: 'Modificado Por', field: 'causer_name', sortable: true },
    { 
        name: 'properties', 
        align: 'left', 
        label: 'Atributo', 
        field: 'properties', 
        sortable: true,
        format: (value) => {
            const { atributo } = value;
            if(!atributo){return ""}
            return `${atributo}`;
        }
    },
    { 
        name: 'properties', 
        align: 'left', 
        label: 'Antes', 
        field: 'properties', 
        sortable: true,
        format: (value) => {
            const { valor_anterior } = value;
            if(!valor_anterior){return ""}
            return `${valor_anterior}`;
        }
    },
    { 
        name: 'properties', 
        align: 'left', 
        label: 'Después', 
        field: 'properties', 
        sortable: true,
        format: (value) => {
            const { valor_actual } = value;
            if(!valor_actual){return ""}
            return `${valor_actual}`;
        }
    },
    { name: 'formatted_updated_at', align: 'center', label: 'Fecha', field: 'formatted_updated_at', sortable: true },
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
        .get("/api/bitacora_general/tabla", {
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
