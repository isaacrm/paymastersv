<template>
    <AppLayout title="Ingresos">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Tipo de ingresos</div>
                    <div class="text-subtitle">
                        Registro de los ingresos del empleado.
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-8">
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="ingreso.nombre"
                                    style="width: 300px"
                                    label="Nombre"
                                    :error-message="
                                        errores.nombre && errores.nombre[0]
                                    "
                                    :error="hayError(errores.nombre)"
                                />
                            </q-item>
                        </div>
                        <br />
                        <div class="col-12 col-md-8">
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="ingreso.descripcion"
                                    class="full-width"
                                    label="Descripción"
                                    :error-message="
                                        errores.descripcion &&
                                        errores.descripcion[0]
                                    "
                                    :error="hayError(errores.descripcion)"
                                />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-4">
                            <q-item>
                                <q-select
                                    style="width: 185px"
                                    v-model="ingreso.forma_aplicacion"
                                    option-value="value"
                                    option-label="label"
                                    emit-value
                                    map-options
                                    :optiones-dense="true"
                                    :options="[
                                        { value: 'P', label: 'Porcentaje' },
                                        { value: 'M', label: 'Monto fijo' },
                                    ]"
                                    :error-message="
                                        errores.forma_aplicacion &&
                                        errores.forma_aplicacion[0]
                                    "
                                    :error="hayError(errores.forma_aplicacion)"
                                    label="Forma de aplicación"
                                    />
                                <br />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-2">
                            <q-item>
                                <q-select
                                    style="width: 185px"
                                    v-model="ingreso.obligatorio"
                                    option-value="value"
                                    option-label="label"
                                    emit-value
                                    map-options
                                    :optiones-dense="true"
                                    :options="[
                                        { value: 'S', label: 'Si' },
                                        { value: 'N', label: 'No' },
                                    ]"
                                    :error-message="
                                        errores.obligatorio &&
                                        errores.obligatorio[0]
                                    "
                                    :error="hayError(errores.obligatorio)"
                                    label="¿Es Obligatorio?"
                                />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-8" v-show="ingreso.forma_aplicacion == 'P'">
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="ingreso.valor_porcentaje"
                                    class="q-pa-sm"
                                    label="Valor porcentaje"
                                    :error-message="
                                        errores.valor_porcentaje &&
                                        errores.valor_porcentaje[0]
                                    "
                                    :error="hayError(errores.valor_porcentaje)"
                                    type="number"
                                    step="0.01"
                                    pattern="[0-9]+([,\.][0-9]+)?"
                                    mask="###0.##"
                                    inputmode="decimal"
                                    :decimals="2"
                                    hint="Ingrese solo valores decimales"
                                />
                            </q-item>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
        <div class="q-pa-md">
            <q-table
                flat
                bordered
                :rows="detalleTabla"
                :columns="columns"
                row-key="id"
                v-model:pagination="pagination"
                :loading="loading"
                :filter="filter"
                binary-state-sort
                :rows-per-page-options="[5, 10, 20, 40, 0]"
                @request="generarTabla"
            >
                <template v-slot:top-right>
                    <q-input
                        borderless
                        dense
                        debounce="300"
                        v-model="filter"
                        placeholder="Buscar"
                    >
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                <template v-slot:top-left>
                    <q-btn
                        outline
                        rounded
                        color="primary"
                        label="Guardar"
                        icon="add"
                        @click="guardar($page.props.auth.user.id)"
                    ></q-btn>
                </template>
                <!--template v-slot:body-cell-operaciones="props">
                    <q-td :props="props">
                        <q-btn
                            round
                            color="warning"
                            icon="edit"
                            class="q-mr-md"
                            @click="editar(props.row)"
                        ></q-btn>
                        <q-btn
                            round
                            color="negative"
                            icon="delete"
                            @click="
                                confirmarEliminar(
                                    props.row.id,
                                    props.row.nombre
                                )
                            "
                        ></q-btn>
                    </q-td>
                </template-->
            </q-table>
        </div>

        <div class="q-pa-md q-gutter-sm">
            <q-dialog v-model="confirmarEliminacion" persistent>
                <q-card>
                    <q-card-section class="row items-center">
                        <q-avatar
                            icon="warning"
                            color="red"
                            text-color="white"
                        />
                        <span class="q-ml-sm"
                            >¿Desea eliminar
                            {{ nombreRegistroEliminar }}?.</span
                        >
                    </q-card-section>

                    <q-card-actions align="right">
                        <q-btn flat label="No" color="primary" v-close-popup />
                        <q-btn
                            flat
                            label="Sí"
                            color="primary"
                            @click="eliminar($props.auth.user.id)"
                            v-close-popup
                        />
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

const $q = useQuasar(); // Para mensajes de exito o error

// De la vista
const detalleTabla = ref();
const submitted = ref(false); // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false);
const ingreso = ref({}); // El objeto que se enviara mediante el request
const confirmarEliminacion = ref(false); // Para modal de eliminacion
const nombreRegistroEliminar = ref(""); // Para que se muestre el nombre en el modal de eliminacion

// Capturar los errores desde laravel.
const errores = ref({}); // Para almacenar el array de errores que viene desde Laravel

// Para el q-table con server-rendering
const filter = ref("");
const loading = ref(false);
const pagination = ref({
    page: 1,
    rowsPerPage: 5,

    rowsNumber: 0,
});
// Fin de fijos e imperativos
// Definiendo las columnas que contendra la tabla. Esto es customizable
const columns = [
    {
        name: "nombre",
        align: "left",
        label: "Nombre",
        field: "nombre",
        sortable: true,
    },
    {
        name: "descripcion",
        align: "left",
        label: "Descripcion",
        field: "descripcion",
        sortable: true,
    },
    {
        name: "forma_aplicacion",
        align: "left",
        label: "Forma aplicacion",
        field: "forma_aplicacion",
        sortable: true,
    },
    //{ name: 'descuento', align: 'left', label: 'descuento', field: 'Descuento', sortable: true },
    {
        name: "obligatorio",
        required: true,
        label: "Obligatorio",
        align: "left",
        field: "obligatorio",
        sortable: true,
    },
    {
        name: "valor_porcentaje",
        required: true,
        label: "Valor porcentaje",
        align: "left",
        field: "valor_porcentaje",
        sortable: true,
    },
   // { name: "operaciones", align: "center", label: "Operaciones" },
];

/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
    await generarTabla({ pagination: pagination.value, filter: filter.value });
});

// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
    ingreso.value = {};
    errores.value = {};
    submitted.value = false;
    errored.value = false;
    confirmarEliminacion.value = false;
    nombreRegistroEliminar.value = "";

    // Actualiza la tabla
    generarTabla({ pagination: pagination.value, filter: filter.value });
};

// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
    if (submitted && valor) return true;
    else return false;
};

// Operacion de guardar
const guardar = async (user_id) => {
    ingreso.value.user_id = user_id;
    submitted.value = true;
    errores.value = {};

    // Actualizar
    if (ingreso.value.id) {
        await axios
            .post("/api/actualizar_ingreso", ingreso.value)
            .then((response) => {
                reiniciarValores();
                // Mensaje de alerta
                $q.notify({
                    type: "positive",
                    message: "Ingreso guardado.",
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
                    message: "Error al agregar el ingreso.",
                });
            });
    }
    // Guardar
    else {
        console.log(ingreso.value);
        await axios
            .post("/api/agregar_ingreso", ingreso.value)
            .then((response) => {
                reiniciarValores();
                // Mensaje de alerta
                $q.notify({
                    type: "positive",
                    message: "Tipo de ingreso guardado.",
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
                    message: "Error al agregar el ingreso del empleado.",
                });
            });
    }
};
// Para mostrar los datos en el form
const editar = (editarIngresos) => {
    ingreso.value = { ...editarIngresos };
    submitted.value = false;
    errores.value = {};
};

// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
    ingreso.value.id = id;
    nombreRegistroEliminar.value = nombre;
    confirmarEliminacion.value = true;
};

// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async (user_id) => {
    ingreso.value.user_id = user_id;
    await axios
        .post("/api/eliminar_registro/" + ingreso.value.id, ingreso.value)
        .then((response) => {
            reiniciarValores();
            // Mensaje de alerta
            $q.notify({
                type: "positive",
                message: "Ingreso eliminado.",
            });
        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify({
                type: "negative",
                message: "Error al eliminar el ingreso.",
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
        .get("/api/tabla_ingresos", {
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
            pagination.value.rowsPerPage =
                response.data.paginacion.filasPorPagina;
            pagination.value.rowsNumber = response.data.paginacion.tuplas;
        })
        .catch((error) => {
            errored.value = true;
        });

    // Apagando el indicador de carga. Este no se toca
    loading.value = false;
};
</script>
