<template>
    <AppLayout title="Empresas">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6">Empresas</div>
                    <div class="text-subtitle">
                        Registro de las empresas.
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="row">
                        <div class="col-12 col-md-8" style="display: flex;">
                            <q-item style="margin-right: 16px;">
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.nombre"
                                    style="width: 250px"
                                    label="Nombre"
                                    :error-message="
                                        errores.nombre && errores.nombre[0]
                                    "
                                    :error="hayError(errores.nombre)"
                                />
                            </q-item>
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.nit"
                                    class="q-mr-md"
                                    style="width: 250px"
                                    label="Número de NIT"
                                    :error-message="
                                        errores.nit &&
                                        errores.nit[0]
                                    "
                                    :error="hayError(errores.nit)"
                                />
                            </q-item>
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.telefono"
                                    style="width: 250px"
                                    label="Número de teléfono"
                                    :error-message="
                                        errores.telefono &&
                                        errores.telefono[0]
                                    "
                                    :error="hayError(errores.telefono)"
                                />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-8" style="display: flex;">
                            <q-item style="margin-right: 16px;">
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.nrc"
                                    style="width: 250px"
                                    label="Número de NRC"
                                    :error-message="
                                        errores.nrc &&
                                        errores.nrc[0]
                                    "
                                    :error="hayError(errores.nrc)"
                                />
                            </q-item>
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.email"
                                    class="q-mr-md"
                                    style="width: 250px"
                                    label="Correo electrónico"
                                    :error-message="
                                        errores.email &&
                                        errores.email[0]
                                    "
                                    :error="hayError(errores.email)"
                                />
                            </q-item>
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.sitio_web"
                                    style="width: 250px"
                                    label="Sitio web"
                                    :error-message="
                                        errores.sitio_web &&
                                        errores.sitio_web[0]
                                    "
                                    :error="hayError(errores.sitio_web)"
                                />
                            </q-item>
                        </div>
                        <div class="col-12 col-md-8" style="display: flex;">
                            <q-item style="margin-right: 16px;">
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.numero_patronal"
                                    style="width: 250px"
                                    label="Número patronal"
                                    :error-message="
                                        errores.numero_patronal &&
                                        errores.numero_patronal[0]
                                    "
                                    :error="hayError(errores.numero_patronal)"
                                />
                            </q-item>
                            <q-item>
                                <q-input
                                    filled
                                    bottom-slots
                                    v-model="empresa.representante_legal"
                                    class="q-mr-md"
                                    style="width: 250px"
                                    label="Representante legal"
                                    :error-message="
                                        errores.representante_legal &&
                                        errores.representante_legal[0]
                                    "
                                    :error="hayError(errores.representante_legal)"
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
                        @click="guardar"
                    ></q-btn>
                </template>
                <template v-slot:body-cell-operaciones="props">
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
                </template>
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
                            @click="eliminar"
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
const empresa = ref({}); // El objeto que se enviara mediante el request
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
        name: "nit",
        align: "left",
        label: "NIT",
        field: "nit",
        sortable: true,
    },
    {
        name: "telefono",
        align: "left",
        label: "Teléfonno",
        field: "telefono",
        sortable: true,
    },
    {
        name: "nrc",
        required: true,
        label: "NCR",
        align: "left",
        field: "nrc",
        sortable: true,
    },
    {
        name: "email",
        required: true,
        label: "Correo electrónico ",
        align: "left",
        field: "email",
        sortable: true,
    },
    {
        name: "sitio_web",
        required: true,
        label: "Sitio web",
        align: "left",
        field: "sitio_web",
        sortable: true,
    },
    {
        name: "numero_patronal",
        required: true,
        label: "Número patronal",
        align: "left",
        field: "numero_patronal",
        sortable: true,
    },
    {
        name: "representante_legal",
        required: true,
        label: "Representante legal",
        align: "left",
        field: "representante_legal",
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
    empresa.value = {};
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
const guardar = async () => {
    submitted.value = true;
    errores.value = {};

    // Actualizar
    if (empresa.value.id) {
        await axios
            .post("/api/actualizar_empresa", empresa.value)
            .then((response) => {
                reiniciarValores();
                // Mensaje de alerta
                $q.notify({
                    type: "positive",
                    message: "empresa guardado.",
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
                    message: "Error al agregar la empresa.",
                });
            });
    }
    // Guardar
    else {
        console.log(empresa.value);
        await axios
            .post("/api/agregar_empresa", empresa.value)
            .then((response) => {
                reiniciarValores();
                // Mensaje de alerta
                $q.notify({
                    type: "positive",
                    message: "Empresa guardada.",
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
                    message: "Error al agregar el registro de la empresa.",
                });
            });
    }
};
// Para mostrar los datos en el form
const editar = (editarempresas) => {
    empresa.value = { ...editarempresas };
    submitted.value = false;
    errores.value = {};
};

// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
    empresa.value.id = id;
    nombreRegistroEliminar.value = nombre;
    confirmarEliminacion.value = true;
};

// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
    await axios
        .post("/api/eliminar_empresa/" + empresa.value.id)
        .then((response) => {
            reiniciarValores();
            // Mensaje de alerta
            $q.notify({
                type: "positive",
                message: "empresa eliminada.",
            });
        })
        .catch((e) => {
            // Mensaje de alerta
            $q.notify({
                type: "negative",
                message: "Error al eliminar la empresa.",
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
        .get("/api/tabla_empresas", {
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