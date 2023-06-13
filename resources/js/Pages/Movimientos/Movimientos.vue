<template>
  <AppLayout title="Movimientos">
    <div class="q-pa-md">
      <q-card class="my-card">
        <q-card-section class="ml-6">
          <div class="text-h6">{{ nombres.mayu + centro_costo_nombre }}</div>
          <div class="text-subtitle">
            Agregar presuouesto a el centro de costo de
            {{ centro_costo_nombre }} de la organizacion.
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-12 col-md-12">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.descripcion"
                  class="full-width"
                  label="Describa el porque de la operacion:"
                  :error-message="errores.descripcion && errores.descripcion[0]"
                  :error="hayError(errores.descripcion)"
                />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-12">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="datos.monto"
                  prefix="$"
                  type="number"
                  class="full-width"
                  label="Asigne el monto de la operacion:"
                  :error-message="errores.monto && errores.monto[0]"
                  :error="hayError(errores.monto)"
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
            @click="guardar($page.props.auth.user.id)"
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
              @click="editar(props.row, $page.props.auth.user.id)"
            ></q-btn>
            <q-btn
              round
              color="negative"
              icon="delete"
              @click="confirmarEliminar(props.row.id, props.row.nombre, $page.props.auth.user.id)"
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

const centro_costo_nombre = ref("");
const centro_costos_id = ref("");

const nombres = {
  minu: "movimientos de ",
  mayu: "Movimientos de ",
  url: "movimientos",
};

const confirmarEliminacion = ref(false);
const nombreRegistroEliminar = ref("");
const errores = ref({});
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
    name: "descripcion",
    align: "left",
    label: "Descripción",
    field: "descripcion",
    sortable: true,
  },
  {
    name: "monto",
    align: "left",
    label: "Monto",
    field: "monto",
  },
  { name: "operaciones", align: "center", label: "Operaciones" },
];
/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
  const searchParams = new URLSearchParams(window.location.search);
  centro_costos_id.value = searchParams.get("ccid");

  await generarTabla({ pagination: pagination.value, filter: filter.value });
  const respuesta = await axios.post("/api/centro_de_costos/obtener_nombre", {
    id: centro_costos_id.value,
  });

  centro_costo_nombre.value = respuesta.data[0].nombre;
});
// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
  datos.value = {};
  errores.value = {};
  submitted.value = false;
  errored.value = false;
  confirmarEliminacion.value = false;
  nombreRegistroEliminar.value = "";
  // Actualiza la tabla
  generarTabla({ pagination: pagination.value, filter: filter.value });
};

const cancelar = () => {
  datos.value = {};
};
// Para mandar comprobar el estado del input y al mismo tiempo determinarlo y mostrar mensaje de error
const hayError = (valor) => {
  if (submitted && valor) return true;
  else return false;
};
// Operacion de guardar
const guardar = async (user_id) => {
  submitted.value = true;
  errores.value = {};

  datos.value.centro_costos_id = centro_costos_id.value;
  datos.value.user_id = user_id;

  if (datos.value.id) {
    await axios
      .post(`/api/${nombres.url}_actualizar`, datos.value)
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
      .post(`/api/${nombres.url}_agregar`, datos.value)
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
const editar = (editardatos, user_id) => {
  datos.value = { ...editardatos };
  datos.value.user_id = user_id;
  submitted.value = false;
  errores.value = {};
};
// Para desplegar el modal
const confirmarEliminar = (id, nombre, user_id) => {
  datos.value.id = id;
  datos.value.user_id = user_id;
  nombreRegistroEliminar.value = nombre;
  confirmarEliminacion.value = true;
};
// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async () => {
  await axios
    .post(`/api/${nombres.url}_eliminar/` + datos.value.id, datos.value)
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
    .get(`/api/${nombres.url}`, {
      params: {
        page,
        rowsPerPage,
        filter,
        centro_costos_id: centro_costos_id.value,
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