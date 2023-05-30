<template>
    <AppLayout title="Detalle Planillas">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <q-btn outline rounded color="primary" label="Añadir Manualmente" icon="add" @click="guardar"></q-btn>
                    <q-btn outline rounded color="danger" label="Regresar" icon="undo" @click="regresar"></q-btn>
                </q-card-section>
            </q-card>
        </div>
        <div class="q-pa-md">
            <q-table title="Detalle de planillas" flat bordered :rows="detalleTabla" :columns="columns" row-key="id"
                v-model:pagination="pagination" :loading="loading" :filter="filter" binary-state-sort
                :rows-per-page-options="[5, 10, 20, 40, 0]" @request="generarTabla">
                <template v-slot:top-right>
                    <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <!-- Nueva plantilla para celdas editables -->
                <template #body-cell-dias_trabajados="props">
                    <q-td :props="props">
                        <template v-if="editableRow === props.index">
                            <q-input v-model="props.row[props.col.field]" dense outlined class="q-pt-none q-pb-none"
                                @input="updateCellValue(props.row, props.col.field)" />
                        </template>
                        <template v-else>
                            <div>{{ props.row[props.col.field] }}</div>
                        </template>
                    </q-td>
                </template>
                <template #body-cell-horas_trabajadas="props">
                    <q-td :props="props">
                        <template v-if="editableRow === props.index">
                            <q-input v-model="props.row[props.col.field]" dense outlined class="q-pt-none q-pb-none"
                                @input="updateCellValue(props.row, props.col.field)" />
                        </template>
                        <template v-else>
                            <div>{{ props.row[props.col.field] }}</div>
                        </template>
                    </q-td>
                </template>
                <template #body-cell-horas_adicionales="props">
                    <q-td :props="props">
                        <template v-if="editableRow === props.index">
                            <q-input v-model="props.row[props.col.field]" dense outlined class="q-pt-none q-pb-none"
                                @input="updateCellValue(props.row, props.col.field)" />
                        </template>
                        <template v-else>
                            <div>{{ props.row[props.col.field] }}</div>
                        </template>
                    </q-td>
                </template>
                <template #body-cell-horas_ausencia="props">
                    <q-td :props="props">
                        <template v-if="editableRow === props.index">
                            <q-input v-model="props.row[props.col.field]" dense outlined class="q-pt-none q-pb-none"
                                @input="updateCellValue(props.row, props.col.field)" />
                        </template>
                        <template v-else>
                            <div>{{ props.row[props.col.field] }}</div>
                        </template>
                    </q-td>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn round color="warning" icon="edit" class="mr-2" @click="editableRow = props.index"
                            v-show="editableRow == -1"></q-btn>
                        <q-btn round color="primary" icon="save" class="mr-2" @click="editableRow = -1"
                            v-show="editableRow != -1"></q-btn>
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
import { onMounted, ref } from "vue";
import { useQuasar } from "quasar";
import AppLayout from "@/Layouts/AppLayout.vue";
import axios from "axios";
/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar(); // Para mensajes de exito o error
// Parametros mandados desde web.php
const props = defineProps({
    idPlanilla: {
        type: String,
        required: true
    }
})

// De la vista
const detalleTabla = ref();
const submitted = ref(false); // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false);
const valoresExistentes = ref({});
const editableRow = ref(-1)
const planillas_id = props.idPlanilla

const datos = ref({}); // El objeto que se enviara mediante el request
const nombres = {
    minu: "planillas",
    mayu: "Detalle Planillas",
};

const confirmarEliminacion = ref(false); // Para modal de eliminacion
const nombreRegistroEliminar = ref(""); // Para que se muestre el nombre en el modal de eliminacion
// Capturar los errores desde laravel. Ademas los componentes necesitan un valor inicial para no generar errores inesperados
const errores = ref({}); // Para almacenar el array de errores que viene desde Laravel
// Para el q-table con server-rendering
// Fijos e imperativos que no se tocan
const filter = ref("");
const loading = ref(false);
const pagination = ref({
    page: 1,
    rowsPerPage: 5,
    /* Cuando se usa server side pagination, QTable necesita
      conocer el "rowsNumber" (Numero total de filas).
      Por qué?
      Porque Quasar no tiene forma de saber cuál será
      la última página sin esta información!
      Por lo tanto, ahora debemos proporcionarle un "número de filas" nosotros mismos.. */
    rowsNumber: 0,
});
// Fin de fijos e imperativos
// Definiendo las columnas que contendra la tabla. Esto es customizable
const columns = [
    {
        name: "nombre_completo",
        align: "left",
        label: "Nombre Completo",
        field: "nombre_completo",
        sortable: true,
    },
    {
        name: "identificacion",
        align: "left",
        label: "Identificación",
        field: "identificacion",
        sortable: true,
    },
    {
        name: "dias_trabajados",
        align: "left",
        label: "Días trabajados",
        field: "dias_trabajados",
        sortable: true,
    },
    {
        name: "horas_trabajadas",
        align: "left",
        label: "Horas Trabajadas",
        field: "horas_trabajadas",
        sortable: true,
    },
    {
        name: "horas_adicionales",
        align: "left",
        label: "Horas Adicionales",
        field: "horas_adicionales",
        sortable: true,
    },
    {
        name: "horas_ausencia",
        align: "left",
        label: "Horas Ausencia",
        field: "horas_ausencia",
        sortable: true,
    },
    {
        name: "salario_base",
        align: "left",
        label: "Salario Base",
        field: "salario_base",
        sortable: true,
    },
    {
        name: "suma_ingresos",
        align: "left",
        label: "Suma de Ingresos",
        field: "suma_ingresos",
        sortable: true,
    },
    {
        name: "suma_descuentos",
        align: "left",
        label: "Suma Descuentos",
        field: "suma_descuentos",
        sortable: true,
    },
    { name: "operaciones", align: "center", label: "Operaciones" },
];

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value });
});
// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    datos.value = {};
    errores.value = {};
    submitted.value = false;
    errored.value = false;
    confirmarEliminacion.value = false;
    nombreRegistroEliminar.value = "";
    valoresExistentes.value = {};
    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value });
};

const regresar = () => {
    window.location.href = '/planillas/';
};

// Operacion de guardar
const guardar = async () => {
    submitted.value = true;
    errores.value = {};
    // Actualizar
    if (datos.value.id) {
        await axios
            .post(`/api/${nombres.minu}_actualizar`, datos.value)
            .then((response) => {
                reiniciarValores();
                // Mensaje de alerta
                $q.notify({
                    type: "positive",
                    message: "datos actualizado.",
                });
            })
            .catch((e) => {
                // Si es un error de tipo 422, es decir, contenido inprocesable
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                }
                // Mensaje de alerta
                $q.notify({
                    type: "negative",
                    message: "Error al agregar el datos.",
                });
            });
    }
    //Guardar datos
    else {
        await axios
            .post(`/api/${nombres.minu}_agregar`, datos.value)
            .then((response) => {
                reiniciarValores();
                $q.notify({
                    type: "positive",
                    message: "datos guardado.",
                });
            })
            .catch((e) => {
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                }
                $q.notify({
                    type: "negative",
                    message: "Error al agregar el datos.",
                });
            });
    }
};
// Para mostrar los datos en el form
const editar = (editardatos) => {
    datos.value = { ...editardatos };
    submitted.value = false;
    errores.value = {};
};
// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
    datos.value.id = id;
    nombreRegistroEliminar.value = nombre;
    confirmarEliminacion.value = true;
};
// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
    await axios
        .post(`/api/${nombres.minu}_eliminar/` + datos.value.id)
        .then((response) => {
            reiniciarValores();
            $q.notify({
                type: "positive",
                message: "datos eliminado.",
            });
        })
        .catch((e) => {
            $q.notify({
                type: "negative",
                message: "Error al eliminar el datos.",
            });
        });
};
/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
    // No se toca
    const { page, rowsPerPage } = props.pagination;
    const filter = props.filter;
    loading.value = true;
    // Obteniendo la tabla de datos
    await axios
        .get('/api/registros', {
            params: {
                page,
                rowsPerPage,
                filter,
                planillas_id
            },
        })
        .then((response) => {
            detalleTabla.value = response.data.detalle;
            // Actualizando el objeto de paginación local. Solamente se cambia la info del response data de ser necesario
            pagination.value.page = response.data.paginacion.pagina;
            pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina;
            pagination.value.rowsNumber = response.data.paginacion.tuplas;
        })
        .catch((error) => {
            errored.value = true;
        });
    // Apagando el indicador de carga. Este no se toca
    loading.value = false;
};

</script>
