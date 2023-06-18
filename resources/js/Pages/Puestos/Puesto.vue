<template>
  <AppLayout title="Puestos">
    <div class="q-pa-md">
      <q-card class="my-card">
        <q-card-section class="ml-6">
          <div class="text-h6">Puestos</div>
          <div class="text-subtitle">
            Registro de los puestos de trabajo de la organizacion.
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-12 col-md-8">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="puesto.nombre"
                  class="full-width"
                  label="Nombre:"
                  :error-message="errores.nombre && errores.nombre[0]"
                  :error="hayError(errores.nombre)"
                  autofocus
                />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="puesto.nro_plazas"
                  type="number"
                  class="full-width"
                  label="Numero de plazas:"
                  :error-message="errores.nro_plazas && errores.nro_plazas[0]"
                  :error="hayError(errores.nro_plazas)"
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
                  v-model="puesto.salario_desde"
                  type="number"
                  class="full-width"
                  label="Salario mínimo:"
                  :error-message="
                    errores.salario_desde && errores.salario_desde[0]
                  "
                  :error="hayError(errores.salario_desde)"
                  prefix="$"
                />
              </q-item>
            </div>
            <div class="col-12 col-md-6">
              <q-item>
                <q-input
                  filled
                  bottom-slots
                  v-model="puesto.salario_hasta"
                  type="number"
                  class="full-width"
                  label="Salario máximo:"
                  :error-message="
                    errores.salario_hasta && errores.salario_hasta[0]
                  "
                  :error="hayError(errores.salario_hasta)"
                  prefix="$"
                />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-12">
              <q-item>
                <q-select
                  v-model="puestoSelect.superior_id"
                  label="Seleccione un superior"
                  :options="consultarPuestos"
                  option-label="name"
                  option-value="id"
                  class="full-width"
                  filled
                  clearable
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
/* VARIABLES Y CONSTANTES, con camelPascal */
// De quasar
const $q = useQuasar(); // Para mensajes de exito o error
// De la vista
const detalleTabla = ref();
const submitted = ref(false); // Para comprobar si se ha dado click en los botones de operaciones
const errored = ref(false);
const puesto = ref({}); // El objeto que se enviara mediante el request
const puestoSelect = ref({}); // El objeto que se enviara mediante el request
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
  sortBy: "nombre",
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
    name: "nro_plazas",
    align: "left",
    label: "Plazas",
    field: "nro_plazas",
    sortable: true,
  },
  {
    name: "salario_desde",
    align: "left",
    label: "Salario minimo",
    field: "salario_desde",
    sortable: true,
  },
  {
    name: "salario_hasta",
    align: "left",
    label: "Salario maximo",
    field: "salario_hasta",
    sortable: true,
  },
  {
    name: "superior_nombre",
    align: "left",
    label: "Superior",
    field: "superior_nombre",
    sortable: true,
  },
  { name: "operaciones", align: "center", label: "Operaciones" },
];

const consultarPuestos = ref([]);
/* METODOS */
// Lo que sucede al cargar por primera vez la vista
onMounted(async () => {
  await generarTabla({ pagination: pagination.value, filter: filter.value });
});
// Para reiniciar los valores luego de realizar alguna operacion
const reiniciarValores = () => {
  puesto.value = {};
  puestoSelect.value = {};
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

const cancelar = () => {
  puesto.value = {};
};

// Operacion de guardar
const guardar = async (user_id) => {
  submitted.value = true;
  errores.value = {};
  puesto.value.user_id = user_id;

  if (puestoSelect.value.superior_id) {
    puesto.value.superior_id = puestoSelect.value.superior_id.id;
  }

  // Actualizar
  if (puesto.value.id) {
    await axios
      .post("/api/puestos_actualizar", puesto.value)
      .then((response) => {
        reiniciarValores();
        // Mensaje de alerta
        $q.notify({
          type: "positive",
          message: "Puesto actualizado.",
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
          message: "Error al agregar el puesto.",
        });
      });
  }
  //Guardar puesto
  else {
    await axios
      .post("/api/puestos_agregar", puesto.value)
      .then((response) => {
        reiniciarValores();
        $q.notify({
          type: "positive",
          message: "Puesto guardado.",
        });
      })
      .catch((e) => {
        if (e.response.status === 422) {
          errores.value = e.response.data.errors;
        }
        $q.notify({
          type: "negative",
          message: "Error al agregar el puesto.",
        });
        puesto.value.superior_id = puesto_por_si_hay_error;
      });
  }
};
// Para mostrar los datos en el form
const editar = (editarPuesto) => {
  // puesto.value = { ...editarPuesto }
  puesto.value.id = editarPuesto.id;
  puesto.value.nombre = editarPuesto.nombre;
  puesto.value.nro_plazas = editarPuesto.nro_plazas;
  puesto.value.salario_desde = editarPuesto.salario_desde;
  puesto.value.salario_hasta = editarPuesto.salario_hasta;
  puestoSelect.value.superior_id = {
    id: editarPuesto.superior_id,
    name: editarPuesto.superior_nombre,
  };
  submitted.value = false;
  errores.value = {};
};
// Para desplegar el modal
const confirmarEliminar = (id, nombre) => {
  puesto.value.id = id;
  nombreRegistroEliminar.value = nombre;
  confirmarEliminacion.value = true;
};
// Elimina definitivamente. En las tablas importantes lo que se hara es modificar un boolean
const eliminar = async (user_id) => {
  puesto.value.user_id = user_id;
  await axios
    .post("/api/puestos_eliminar/" + puesto.value.id, puesto.value)
    .then((response) => {
      reiniciarValores();
      $q.notify({
        type: "positive",
        message: "Puesto eliminado.",
      });
    })
    .catch((e) => {
      $q.notify({
        type: "negative",
        message: "Error al eliminar el puesto.",
      });
    });
};
/* EXCLUSIVO DE TABLA */
const generarTabla = async (props) => {
  // No se toca
  const { page, rowsPerPage, sortBy, descending } = props.pagination;
  const filter = props.filter;
  loading.value = true;
  // Obteniendo la tabla de datos
  await axios
    .get("/api/puestos", {
      params: {
        page,
        rowsPerPage,
        filter,
        sortBy,
        descending: descending ? 0 : 1,
      },
    })
    .then((response) => {
      detalleTabla.value = response.data.detalle;
      // Actualizando el objeto de paginación local. Solamente se cambia la info del response data de ser necesario
      pagination.value.page = response.data.paginacion.pagina;
      pagination.value.rowsPerPage = response.data.paginacion.filasPorPagina;
      pagination.value.rowsNumber = response.data.paginacion.tuplas;
      pagination.value.sortBy = response.data.paginacion.ordenarPor;
      pagination.value.descending = descending;
    })
    .catch((error) => {
      errored.value = true;
    });
  // Apagando el indicador de carga. Este no se toca
  loading.value = false;

  // * Los valores de select para recargarlos
  await obtenerPuestos();
};

const obtenerPuestos = async () => {
  await axios.get("/api/puestos_consultar_puestos").then((response) => {
    consultarPuestos.value = response.data;
  });
};
</script>