<template>
    <AppLayout title="Dashboard">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Techo Laboral</div>
                    <div class="text-subtitle">Según la legislación salvadoreña, es el máximo al cuál se le puede aplicar un
                        determinado descuento.</div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="techoLaboral.anyo" class="full-width" type="number"
                                    label="Año de vigencia" :error-message="errores.anyo && errores.anyo[0]"
                                    :error="errores.hasOwnProperty('anyo')" autofocus suffix="año"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="techoLaboral.afp" type="number"
                                    class="full-width" label="Techo AFP"
                                    :error-message="errores.afp && errores.afp[0]"
                                    :error="errores.hasOwnProperty('afp')" prefix="$"/>
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="techoLaboral.isss" type="number"
                                    class="full-width" label="Techo ISSS"
                                    :error-message="errores.isss && errores.isss[0]"
                                    :error="errores.hasOwnProperty('isss')" prefix="$"/>
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
                        <q-btn round color="warning" icon="edit" class="mr-2" @click="editar(props.row)"></q-btn>
                        <q-btn round color="negative" icon="delete"
                            @click="confirmarEliminar(props.row.id, props.row.anyo)"></q-btn>
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
                        <span class="q-ml-sm">¿Desea eliminar los techos laborales del año {{ nombreRegistroEliminar
                        }}?.</span>
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
import { format, useQuasar } from 'quasar'
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar() // Para mensajes de exito o error

// De la vista
const detalleTabla = ref()
const submitted = ref(false) // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false)
const techoLaboral = ref({}) // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false) // Para modal de eliminacion
const nombreRegistroEliminar = ref('') // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}) // Para almacenar el array de errores que viene desde Laravel

// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref('')
const loading = ref(false)
const pagination = ref({
    sortBy: 'anyo', // Se actualiza segun columna de ordenamiento por defecto
    descending: true, // true para descendente (mayor a menor) false para ascendente (menor a mayor)
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
    { name: 'anyo', align: 'left', label: 'Año de vigencia', field: 'anyo', sortable: true },
    { name: 'afp', align: 'left', label: 'AFP', field: 'afp', sortable: true, format: formatoDinero },
    { name: 'isss', align: 'left', label: 'ISSS', field: 'isss', sortable: true, format: formatoDinero },
    { name: 'operaciones', align: 'center', label: 'Operaciones' }
]

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value })
})

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    techoLaboral.value = {}
    errores.value = {}
    submitted.value = false
    errored.value = false
    confirmarEliminacion.value = false
    nombreRegistroEliminar.value = ''

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value })
}

const cancelar = () => {
    techoLaboral.value = {}
}

// Operacion de guardar
const guardar = async () => {
    submitted.value = true
    errores.value = {}

    // Actualizar
    if (techoLaboral.value.id) {
        await axios
            .post("/api/techo_laboral/actualizar", techoLaboral.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Techo laboral guardado.'
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
                        message: 'Error al agregar el techo laboral.'
                    }
                )
            })
    }
    // Guardar
    else {
        await axios
            .post("/api/techo_laboral/agregar", techoLaboral.value)
            .then((response) => {
                reiniciarValores()
                // Mensaje de alerta
                $q.notify(
                    {
                        type: 'positive',
                        message: 'Techo laboral guardado.'
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
                        message: 'Error al agregar el techo laboral.'
                    }
                )
            })
    }
}
// Para mostrar los datos en el form
const editar = (editarTechoLaboral) => {
    techoLaboral.value = { ...editarTechoLaboral }
    submitted.value = false;
    errores.value = {}
}

// Para desplegar el modal
const confirmarEliminar = (id, anyo) => {
    techoLaboral.value.id = id
    nombreRegistroEliminar.value = anyo
    confirmarEliminacion.value = true
}


// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
    await axios
        .post("/api/techo_laboral/eliminar/" + techoLaboral.value.id)
        .then((response) => {
            reiniciarValores()
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'positive',
                    message: 'Techo laboral eliminado.'
                }
            )

        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify(
                {
                    type: 'negative',
                    message: 'Error al eliminar el techo laboral.'
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
        .get("/api/techo_laboral/tabla", {
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
