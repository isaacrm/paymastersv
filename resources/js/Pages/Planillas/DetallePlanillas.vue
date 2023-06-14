<template>
    <AppLayout title="Detalle Planillas">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <q-btn outline rounded color="primary" label="Añadir Manualmente" icon="add" @click="guardar"></q-btn>
                    <q-btn outline rounded color="secondary" label="Planilla General" icon="picture_as_pdf"
                        @click="generarPlanillaGeneral"></q-btn>
                    <q-btn outline rounded color="danger" label="Regresar" icon="undo" @click="regresar"></q-btn>
                </q-card-section>
            </q-card>
        </div>
        <div class="q-pa-md">
            <q-table title="Detalle de planillas" flat bordered :rows="detalleTabla" :columns="columns" row-key="name"
                v-model:pagination="pagination" :loading="loading" :filter="filter" binary-state-sort
                :rows-per-page-options="[5, 10, 20, 40, 0]" @request="generarTabla">
                <template v-slot:top-right>
                    <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <template v-slot:header="props">
                    <q-tr :props="props">
                        <q-th auto-width />
                        <q-th v-for="col in props.cols" :key="col.name" :props="props">
                            {{ col.label }}
                        </q-th>
                        <q-th>
                            Operaciones
                        </q-th>
                    </q-tr>
                </template>
                <template v-slot:body="props">
                    <q-tr :props="props">
                        <q-td auto-width>
                            <q-btn size="sm" color="accent" round dense @click="props.expand = !props.expand"
                                :icon="props.expand ? 'remove' : 'add'" />
                        </q-td>
                        <q-td v-for="col in props.cols" :key="col.name" :props="props">
                            {{ col.value }}
                        </q-td>
                        <q-td>
                            <q-btn round color="black" icon="picture_as_pdf" class="mr-2" @click="generarPagoIndividual(props.row.id)"></q-btn>
                            <q-btn round color="warning" icon="edit" class="mr-2"></q-btn>
                            <q-btn round color="negative" icon="delete"
                                @click="confirmarEliminar(props.row.id, props.row.nombre)"></q-btn>
                        </q-td>
                    </q-tr>
                    <q-tr v-show="props.expand" :props="props">
                        <q-td colspan="100%">
                            <div class="text-left">Salario Base: {{ props.row.salario_base }} | Suma de ingresos: {{
                                props.row.total_ingresos }} | Salario Total: {{ props.row.salario_total }} | Suma de
                                descuentos: {{ props.row.total_descuentos }} | Líquido a recibir: {{ props.row.salario_liquido }} |</div>
                        </q-td>
                    </q-tr>
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
const generar = ref({})
const valoresExistentes = ref({});
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
        name: "dias_ausente",
        align: "left",
        label: "Días Ausente",
        field: "dias_ausente",
        sortable: true,
    },
    {
        name: "dias_permiso",
        align: "left",
        label: "Días permiso",
        field: "dias_permiso",
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
    }
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
    generar.value = {};
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
                    message: "Detalle de planilla actualizada.",
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
                    message: "Error al agregar el detalle de planilla.",
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
                    message: "Detalle de planilla guardado.",
                });
            })
            .catch((e) => {
                if (e.response.status === 422) {
                    errores.value = e.response.data.errors;
                }
                $q.notify({
                    type: "negative",
                    message: "Error al agregar el detalle de planilla.",
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
                message: "Detalle de planilla eliminado.",
            });
        })
        .catch((e) => {
            $q.notify({
                type: "negative",
                message: "Error al eliminar el detalle de planilla.",
            });
        });
};

const generarPlanillaGeneral = async () => {
    generar.value.planillas_id = planillas_id
    await axios
        .post('/api/pdf_planilla_general', generar.value, { responseType: 'blob' })
        .then(response => {
            const blob = new Blob([response.data], {
                type: 'application/pdf'
            })
            const url = window.URL.createObjectURL(blob)
            window.open(url)
        })
        .catch(error => {
            $q.notify({
                type: "negative",
                message: "Error al generar el archivo.",
            });
        })
}

const generarPagoIndividual = async (registroId) => {
    generar.value.planillas_id = planillas_id
    generar.value.registro_id = registroId
    await axios
        .post('/api/pdf_pago_personal', generar.value, { responseType: 'blob' })
        .then(response => {
            const blob = new Blob([response.data], {
                type: 'application/pdf'
            })
            const url = window.URL.createObjectURL(blob)
            window.open(url)
        })
        .catch(error => {
            $q.notify({
                type: "negative",
                message: "Error al generar el archivo.",
            });
        })
}

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
