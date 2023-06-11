<template>
  <AppLayout title="Centro de costos">
    <div class="q-pa-md">
      <q-card class="my-card">
        <q-card-section class="ml-6">
          <div class="text-h6">{{ nombres.nombreMayuscula }}</div>
          <div class="text-subtitle">
            Registro de los {{ nombres.nombreMinuscula }} de trabajo de la
            organizacion.
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-12 col-md-12">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.nombre"
                  class="full-width"
                  label="Nombre del centro de costo:"
                  :error-message="errores.nombre && errores.nombre[0]"
                  :error="hayError(errores.nombre)" autofocus
                />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-select
                  filled
                  bottom-slots
                  class="full-width"
                  v-model="datos.mes_del"
                  :options="meses"
                  label="Mes desde"
                />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-select
                  filled
                  bottom-slots
                  class="full-width"
                  v-model="datos.mes_al"
                  :options="meses"
                  label="Mes hasta"
                />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.anyo"
                  class="full-width"
                  label="Año:"
                  mask="####"
                  fill-mask="#"
                  :error-message="errores.anyo && errores.anyo[0]"
                  :error="hayError(errores.anyo)"
                />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.presupuesto_inicial"
                  type="number"
                  class="full-width"
                  label="Presupuesto inicial:"
                  :error-message="
                    errores.presupuesto_inicial &&
                    errores.presupuesto_inicial[0]
                  "
                  :error="hayError(errores.presupuesto_inicial)"
                  prefix="$"
                />
              </q-item>
            </div>
            <div class="col-12 col-md-6">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.presupuesto_restante"
                  type="number"
                  class="full-width"
                  label="Presupuesto restante:"
                  :error-message="
                    errores.presupuesto_restante &&
                    errores.presupuesto_restante[0]
                  "
                  :error="hayError(errores.presupuesto_restante)"
                  prefix="$"
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
          <div class="q-gutter-sm">
          <q-btn
            outline
            rounded
            color="primary"
            label="Guardar"
            icon="add"
            @click="guardar"
          ></q-btn>
          <q-btn
            outline
            rounded
            color="danger"
            label="Cancelar"
            icon="cancel"
            @click="cancelar"
          ></q-btn>
          </div>
        </template>
        <template v-slot:body-cell-operaciones="props">
          <q-td :props="props">
            <div class="q-gutter-sm">
            <q-btn
              round
              color="warning"
              icon="edit"
              class="mr-2"
              @click="editar(props.row)"
            ></q-btn>
            <q-btn
              round
              color="negative"
              icon="delete"
              @click="confirmarEliminar(props.row.id, props.row.nombre)"
            ></q-btn>
            <q-btn
              round
              color="secondary"
              icon="list"
              @click="confirmarMovimiento(props.row.id)"
            ></q-btn>
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
            <span class="q-ml-sm"
              >¿Desea eliminar {{ nombreRegistroEliminar }}?.</span
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
// De quasar
const $q = useQuasar(); // Para mensajes de exito o error
// De la vista
const detalleTabla = ref();
const submitted = ref(false); // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false);

const datos = ref({}); // El objeto que se enviara mediante el request
const movimientos = ref({});
const nombres = {
  minu: "centro_de_costo",
  mayu: "centro_de_costo",
  nombreMinuscula: "centro de costos",
  nombreMayuscula: "Centro de costos",
};

const confirmarEliminacion = ref(false); // Para modal de eliminacion
const confirmarRealizacionMovimiento = ref(false);
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
    name: "nombre",
    align: "left",
    label: "Nombre:",
    field: "nombre",
    sortable: true,
  },
  {
    name: "mes_del",
    align: "left",
    label: "Desde el mes:",
    field: "mes_del",
    sortable: true,
  },
  {
    name: "mes_al",
    align: "left",
    label: "Hasta el mes:",
    field: "mes_al",
    sortable: true,
  },
  { name: "anyo", align: "left", label: "Año", field: "anyo", sortable: true },
  {
    name: "presupuesto_inicial",
    align: "left",
    label: "Presupuesto inicial",
    field: "presupuesto_inicial",
    sortable: true,
  },
  {
    name: "presupuesto_restante",
    align: "left",
    label: "Presupuesto restante",
    field: "presupuesto_restante",
    sortable: true,
  },
  { name: "operaciones", align: "center", label: "Operaciones" },
];

const meses = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
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

  movimientos.value = {};
  confirmarRealizacionMovimiento.value = false;

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
const guardar = async () => {
  submitted.value = true;
  errores.value = {};
  // Actualizar
  if (datos.value.id) {
    await axios
      .post(`/api/${nombres.minu}s_actualizar`, datos.value)
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
      .post(`/api/${nombres.minu + "s"}_agregar`, datos.value)
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

const confirmarMovimiento = (id) => {
  window.location.href = `/movimientos?ccid=${id}`;
};

// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
  await axios
    .post(`/api/${nombres.minu + "s"}_eliminar/` + datos.value.id)
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
    .get(`/api/${nombres.minu + "s"}`, {
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
</script>
