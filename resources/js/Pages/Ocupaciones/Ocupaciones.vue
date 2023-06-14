<template>
  <AppLayout title="Ocupaciones">
    <div class="q-pa-md">
      <q-card class="my-card">
        <q-card-section class="ml-6">
          <div class="text-h6">{{ nombres.mayus }}</div>
          <div class="text-subtitle">Registro de las {{ nombres.minus }}.</div>
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
                  label="Nombre de la ocupacion:"
                  :error-message="errores.nombre && errores.nombre[0]"
                  :error="hayError(errores.nombre)"
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
                @click="editar(props.row)"
              ></q-btn>
              <q-btn
                round
                color="negative"
                icon="delete"
                @click="confirmarEliminar(props.row.id, props.row.nombre)"
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
              @click="eliminar($page.props.auth.user.id)"
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

const $q = useQuasar();

const detalleTabla = ref();
const submitted = ref(false);
const errored = ref(false);

const datos = ref({});
const nombres = {
  mayus: "Ocupaciones",
  minus: "ocupaciones",
  link: "ocupaciones",
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
const columns = [
  {
    name: "nombre",
    align: "left",
    label: "Nombre:",
    field: "nombre",
    sortable: true,
  },
  { name: "operaciones", align: "center", label: "Operaciones" },
];

onMounted(async () => {
  await generarTabla({ pagination: pagination.value, filter: filter.value });
});

const reiniciarValores = () => {
  datos.value = {};
  errores.value = {};
  submitted.value = false;
  errored.value = false;
  confirmarEliminacion.value = false;
  nombreRegistroEliminar.value = "";

  generarTabla({ pagination: pagination.value, filter: filter.value });
};

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
      .post(`/api/${nombres.link}_actualizar`, datos.value)
      .then((response) => {
        reiniciarValores();
        $q.notify({
          type: "positive",
          message: "Ocupación actualizada.",
        });
      })
      .catch((e) => {
        if (e.response.status === 422) {
          errores.value = e.response.data.errors;
        }
        $q.notify({
          type: "negative",
          message: "Error al agregar la ocupación.",
        });
      });
  } else {
    await axios
      .post(`/api/${nombres.link}_agregar`, datos.value)
      .then((response) => {
        reiniciarValores();
        $q.notify({
          type: "positive",
          message: "Ocupación guardada.",
        });
      })
      .catch((e) => {
        if (e.response.status === 422) {
          errores.value = e.response.data.errors;
        }
        $q.notify({
          type: "negative",
          message: "Error al agregar la ocupación.",
        });
      });
  }
};

const editar = (editardatos) => {
  datos.value = { ...editardatos };
  submitted.value = false;
  errores.value = {};
};

const confirmarEliminar = (id, nombre) => {
  datos.value.id = id;
  nombreRegistroEliminar.value = nombre;
  confirmarEliminacion.value = true;
};

const eliminar = async (user_id) => {
  datos.value.user_id = user_id;
  await axios
    .post(`/api/${nombres.link}_eliminar/` + datos.value.id, datos.value)
    .then((response) => {
      reiniciarValores();
      $q.notify({
        type: "positive",
        message: "Ocupación eliminada.",
      });
    })
    .catch((e) => {
      $q.notify({
        type: "negative",
        message: "Error al eliminar la ocupación.",
      });
    });
};

const generarTabla = async (props) => {
  const { page, rowsPerPage } = props.pagination;
  const filter = props.filter;
  loading.value = true;
  await axios
    .get(`/api/${nombres.link}`, {
      params: {
        page,
        rowsPerPage,
        filter,
      },
    })
    .then((response) => {
      detalleTabla.value = response.data.detalle;
      pagination.value.page = response.data.paginacion.pagina;
      pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina;
      pagination.value.rowsNumber = response.data.paginacion.tuplas;
    })
    .catch((error) => {
      errored.value = true;
    });
  loading.value = false;
};
</script>
