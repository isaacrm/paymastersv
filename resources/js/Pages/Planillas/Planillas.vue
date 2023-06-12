<template>
    <AppLayout title="Planillas">
        <div class="q-pa-md q-gutter-sm"
            v-show="valoresExistentes.cantidadEmpleados <= 0 || valoresExistentes.cantidadDescuentos <= 0">
            <q-banner inline-actions class="text-white bg-red" v-show="valoresExistentes.cantidadEmpleados <= 0">
                No hay empleados registrados.
            </q-banner>
            <q-banner inline-actions class="text-white bg-red" v-show="valoresExistentes.cantidadDescuentos <= 0">
                No hay descuentos obligatorios registrados para aplicar a los salarios.
            </q-banner>
        </div>
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">{{ nombres.mayu }}</div>
                    <div class="text-subtitle">
                        Registro de las {{ nombres.minu }} de trabajo de la organizacion.
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-select filled bottom-slots class="full-width" v-model="datos.mes_periodo"
                                    :options="meses" label="Mes periodo"
                                    :error-message="errores.mes_periodo && errores.mes_periodo[0]"
                                    :error="hayError(errores.mes_periodo)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-6">
                            <q-item>
                                <q-input filled bottom-slots v-model="datos.anyo_periodo" class="full-width"
                                    label="Año periodo:" mask="####" fill-mask="#" hint="Año:####" :error-message="errores.anyo_periodo && errores.anyo_periodo[0]
                                        " :error="hayError(errores.anyo_periodo)" />
                            </q-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="datos.fecha_generacion" class="full-width"
                                    label="Fecha de generacion:" type="date" :error-message="errores.fecha_generacion && errores.fecha_generacion[0]
                                        " :error="hayError(errores.fecha_generacion)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="datos.dias_laborales" type="number" class="full-width"
                                    label="Dias laborales:" :error-message="errores.dias_laborales && errores.dias_laborales[0]
                                        " :error="hayError(errores.dias_laborales)" />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-input filled bottom-slots v-model="datos.horas_laborales" type="number"
                                    class="full-width" label="Horas laborales:" :error-message="errores.horas_laborales && errores.horas_laborales[0]
                                        " :error="hayError(errores.horas_laborales)" />
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
                    <q-btn outline rounded color="primary" label="Guardar" icon="add" @click="guardar($page.props.auth.user.id)"
                        v-show="valoresExistentes.cantidadDescuentos > 0 && valoresExistentes.cantidadEmpleados > 0"></q-btn>
                    <q-btn outline rounded color="danger" label="Cancelar" icon="cancel" @click="cancelar"></q-btn>
                </template>
                <template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn round color="secondary" icon="table_view" class="mr-2"
                            @click="redireccionDetalle(props.row.id)"></q-btn>
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
                        <q-btn flat label="Sí" color="primary" @click="eliminar($page.props.auth.user.id)" v-close-popup />
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
import { meses } from "../../Components/Constantes";
/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar(); // Para mensajes de exito o error
// De la vista
const detalleTabla = ref();
const submitted = ref(false); // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false);
const valoresExistentes = ref({});

const datos = ref({}); // El objeto que se enviara mediante el request
const nombres = {
    minu: "planillas",
    mayu: "Planillas",
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
        name: "fecha_generacion",
        align: "left",
        label: "Fecha de generacion:",
        field: "fecha_generacion",
        sortable: true,
    },
    {
        name: "mes_periodo",
        align: "left",
        label: "Mes periodo:",
        field: "mes_periodo",
        sortable: true,
    },
    {
        name: "anyo_periodo",
        align: "left",
        label: "Año:",
        field: "anyo_periodo",
        sortable: true,
    },
    {
        name: "dias_laborales",
        align: "left",
        label: "Dias laborales: ",
        field: "dias_laborales",
        sortable: true,
    },
    {
        name: "horas_laborales",
        align: "left",
        label: "Horas laborales:",
        field: "horas_laborales",
        sortable: true,
    },
    { name: "operaciones", align: "center", label: "Operaciones" },
];

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value });
});

const redireccionDetalle = async (planillas_id) => {
    window.location.href = '/detalle_planillas/' + planillas_id;
};

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
// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor) return true;
    else return false;
};

const cancelar = () => {
    datos.value = {};
};

// Operacion de guardar
const guardar = async (user_id) => {
    submitted.value = true;
    errores.value = {};
    datos.value.user_id = user_id;
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
const eliminar = async (user_id) => {
    datos.value.user_id = user_id;
    await axios
        .post(`/api/${nombres.minu}_eliminar/` + datos.value.id, datos.value)
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
    // Verifica siempre si existe Empleado y Descuentos
    await comprobacionInicial()
    // No se toca
    const { page, rowsPerPage } = props.pagination;
    const filter = props.filter;
    loading.value = true;
    // Obteniendo la tabla de datos
    await axios
        .get(`/api/${nombres.minu}`, {
            params: {
                page,
                rowsPerPage,
                filter,
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

// Comprobando que existan Empleados y Descuentos para aplicar en planilla
const comprobacionInicial = async () => {
    await axios
        .get(`/api/${nombres.minu}/comprobacion`)
        .then((response) => {
            valoresExistentes.value.cantidadDescuentos = response.data.descuentosExistentes;
            valoresExistentes.value.cantidadEmpleados = response.data.empleadosExistentes;
        })
        .catch((error) => {
            errored.value = true;
        });
}

</script>
