<template>
  <AppLayout title="Empleados">
    <div class="q-pa-md">
      <q-card class="my-card">
        <q-card-section class="ml-6">
          <div class="text-h6">{{ nombres.mayus }}</div>
          <div class="text-subtitle">Registro de los {{ nombres.minus }}.</div>
        </q-card-section>
        <q-card-section>
          <div class="text-h7 text-weight-bold">Datos Personales</div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.primer_nombre" class="full-width"
                  label="Primer nombre del empleado:" :error-message="
                    errores.primer_nombre && errores.primer_nombre[0]
                  " :error="hayError(errores.primer_nombre)" autofocus />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.segundo_nombre" class="full-width"
                  label="Segundo nombre del empleado:" :error-message="
                    errores.segundo_nombre && errores.segundo_nombre[0]
                  " :error="hayError(errores.segundo_nombre)" />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.apellido_paterno" class="full-width"
                  label="Apellido paterno del empleado:" :error-message="
                    errores.apellido_paterno && errores.apellido_paterno[0]
                  " :error="hayError(errores.apellido_paterno)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.apellido_materno" class="full-width"
                  label="Apellido materno del empleado:" :error-message="
                    errores.apellido_materno && errores.apellido_materno[0]
                  " :error="hayError(errores.apellido_materno)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.apellido_casada" class="full-width"
                  label="Apellido de casada del empleado:" :error-message="
                    errores.apellido_casada && errores.apellido_casada[0]
                  " :error="hayError(errores.apellido_casada)" />
              </q-item>
            </div>
          </div>

          <div class="text-h7 text-weight-bold">Información Personal</div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.tipo_documento" label="Seleccione el tipo de documento:"
                  :options="tipo_documento" option-label="name" option-value="id" class="full-width" filled clearable
                  :error-message="
                    errores.tipo_documentos_id && errores.tipo_documentos_id[0]
                  " :error="hayError(errores.tipo_documentos_id)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.identificacion" class="full-width"
                  label="Identificacion del empleado:" :error-message="
                    errores.identificacion && errores.identificacion[0]
                  " :error="hayError(errores.identificacion)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.nit" class="full-width" label="Nit del empleado:"
                  mask="####-######-###-#" fill-mask="_"
                  :error-message="errores.nit && errores.nit[0]" :error="hayError(errores.nit)" />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.fecha_nacimiento" class="full-width"
                  label="Fecha de nacimiento:" type="date" :error-message="
                    errores.fecha_nacimiento && errores.fecha_nacimiento[0]
                  " :error="hayError(errores.fecha_nacimiento)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.estado_civil" label="Seleccione el estado civil:" :options="estado_civil"
                  option-label="name" option-value="id" class="full-width" filled clearable :error-message="
                    errores.estados_civiles_id && errores.estados_civiles_id[0]
                  " :error="hayError(errores.estados_civiles_id)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.genero" label="Seleccione el genero:" :options="genero" option-label="name"
                  option-value="id" class="full-width" filled clearable
                  :error-message="errores.generos_id && errores.generos_id[0]" :error="hayError(errores.generos_id)" />
              </q-item>
            </div>
          </div>


          <div class="text-h7 text-weight-bold">Información de contacto</div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.email_personal" class="full-width"
                  label="Email personal de el empleado:" type="email" :error-message="
                    errores.email_personal && errores.email_personal[0]
                  " :error="hayError(errores.email_personal)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.direccion" label="Seleccione la direccion:" :options="direccion"
                  option-label="name" option-value="id" class="full-width" filled clearable :error-message="
                    errores.direcciones_id && errores.direcciones_id[0]
                  " :error="hayError(errores.direcciones_id)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.email_profesional" class="full-width"
                  label="Email profesional de el empleado:" type="email" :error-message="
                    errores.email_profesional && errores.email_profesional[0]
                  " :error="hayError(errores.email_profesional)" />
              </q-item>
            </div>
          </div>

          <div class="text-h7 text-weight-bold">Información Laboral</div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.ocupacion" label="Seleccione la ocupacion:" :options="ocupacion"
                  option-label="name" option-value="id" class="full-width" filled clearable :error-message="
                    errores.ocupaciones_id && errores.ocupaciones_id[0]
                  " :error="hayError(errores.ocupaciones_id)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-select v-model="datosSelect.puesto" label="Seleccione el puesto:" :options="puesto" option-label="name"
                  option-value="id" class="full-width" filled clearable
                  :error-message="errores.puestos_id && errores.puestos_id[0]" :error="hayError(errores.puestos_id)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.fecha_ingreso" class="full-width" label="Fecha de ingreso:"
                  type="date" :error-message="
                    errores.fecha_ingreso && errores.fecha_ingreso[0]
                  " :error="hayError(errores.fecha_ingreso)" />
              </q-item>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.salario_base" class="full-width"
                  label="Salario base del empleado:" prefix="$" type="number" :error-message="
                    errores.salario_base && errores.salario_base[0]
                  " :error="hayError(errores.salario_base)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.isss" class="full-width"
                mask="#########" fill-mask="_"  
                label="Numero del ISSS del empleado:" :error-message="errores.isss && errores.isss[0]"
                  :error="hayError(errores.isss)" />
              </q-item>
            </div>
            <div class="col-12 col-md-4">
              <q-item>
                <q-input filled bottom-slots v-model="datos.nup" class="full-width"
                  mask="############" fill-mask="_" 
                  label="Ingrese el nup de el empleado:"
                  :error-message="errores.nup && errores.nup[0]" :error="hayError(errores.nup)" />
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
                @click="confirmarEliminar(props.row.id, props.row.nombre)"></q-btn>
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

const $q = useQuasar();

const detalleTabla = ref();
const submitted = ref(false);
const errored = ref(false);

const datos = ref({});
const nombres = {
  mayus: "Empleados",
  minus: "empleados",
  link: "empleados",
};
// * Para los selects
const datosSelect = ref({});
const estado_civil = ref([]);
const genero = ref([]);
const ocupacion = ref([]);
const tipo_documento = ref([]);
const direccion = ref([]);
const puesto = ref([]);

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
    name: "primer_nombre",
    align: "left",
    label: "Primer nombre:",
    field: "primer_nombre",
    sortable: true,
  },
  {
    name: "segundo_nombre",
    align: "left",
    label: "Segundo nombre:",
    field: "segundo_nombre",
    sortable: true,
  },
  {
    name: "apellido_paterno",
    align: "left",
    label: "Apellido paterno:",
    field: "apellido_paterno",
    sortable: true,
  },
  {
    name: "apellido_materno",
    align: "left",
    label: "Apellido materno:",
    field: "apellido_materno",
    sortable: true,
  },
  {
    name: "identificacion",
    align: "left",
    label: "Identificacion:",
    field: "identificacion",
    sortable: true,
  },
  { name: "operaciones", align: "center", label: "Operaciones" },
];

onMounted(async () => {
  await generarTabla({ pagination: pagination.value, filter: filter.value });
});

const reiniciarValores = () => {
  datos.value = {};
  datosSelect.value = {};
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
  datosSelect.value = {};
};

// Operacion de guardar
const guardar = async () => {
  submitted.value = true;
  errores.value = {};

  if (datosSelect.value.estado_civil) {
    datos.value.estados_civiles_id = datosSelect.value.estado_civil.id;
  }

  if (datosSelect.value.genero) {
    datos.value.generos_id = datosSelect.value.genero.id;
  }

  if (datosSelect.value.ocupacion) {
    datos.value.ocupaciones_id = datosSelect.value.ocupacion.id;
  }

  if (datosSelect.value.tipo_documento) {
    datos.value.tipo_documentos_id = datosSelect.value.tipo_documento.id;
  }

  if (datosSelect.value.direccion) {
    datos.value.direcciones_id = datosSelect.value.direccion.id;
  }

  if (datosSelect.value.puesto) {
    datos.value.puestos_id = datosSelect.value.puesto.id;
  }

  // Actualizar
  if (datos.value.id) {
    await axios
      .post(`/api/${nombres.link}/actualizar`, datos.value)
      .then((response) => {
        reiniciarValores();
        $q.notify({
          type: "positive",
          message: "datos actualizado.",
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
  } else {
    await axios
      .post(`/api/${nombres.link}/agregar`, datos.value)
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

const editar = (editardatos) => {
  datos.value.id = editardatos.id;
  datos.value.primer_nombre = editardatos.primer_nombre;
  datos.value.segundo_nombre = editardatos.segundo_nombre;
  datos.value.apellido_paterno = editardatos.apellido_paterno;
  datos.value.apellido_materno = editardatos.apellido_materno;
  datos.value.apellido_casada = editardatos.apellido_casada;
  datos.value.identificacion = editardatos.identificacion;
  datos.value.nit = editardatos.nit;
  datos.value.isss = editardatos.isss;
  datos.value.nup = editardatos.nup;
  datos.value.email_personal = editardatos.email_personal;
  datos.value.email_profesional = editardatos.email_profesional;
  datos.value.fecha_nacimiento = editardatos.fecha_nacimiento;
  datos.value.fecha_ingreso = editardatos.fecha_ingreso;
  datos.value.salario_base = editardatos.salario_base;
  datosSelect.value.estado_civil = {
    id: editardatos.estado_civil_id,
    name: editardatos.estado_civil_nombre,
  };
  datosSelect.value.genero = {
    id: editardatos.genero_id,
    name: editardatos.genero_nombre,
  };
  datosSelect.value.ocupacion = {
    id: editardatos.ocupacion_id,
    name: editardatos.ocupacion_nombre,
  };
  datosSelect.value.tipo_documento = {
    id: editardatos.tipo_documento_id,
    name: editardatos.tipo_documento_nombre,
  };
  datosSelect.value.direccion = {
    id: editardatos.direccion_id,
    name: editardatos.direccion_nombre,
  };
  datosSelect.value.puesto = {
    id: editardatos.puesto_id,
    name: editardatos.puesto_nombre,
  };

  submitted.value = false;
  errores.value = {};
};

const confirmarEliminar = (id, nombre) => {
  datos.value.id = id;
  nombreRegistroEliminar.value = nombre;
  confirmarEliminacion.value = true;
};

const eliminar = async () => {
  await axios
    .post(`/api/${nombres.link}/eliminar/` + datos.value.id)
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

  await actualizar_selects();
};

const obtenerEstadoCivil = async () => {
  await axios.get("/api/estados_civiles_consultar_select").then((response) => {
    estado_civil.value = response.data;
  });
};

const obtenerGeneros = async () => {
  await axios.get("/api/generos_consultar_select").then((response) => {
    genero.value = response.data;
  });
};

const obtenerOcupacion = async () => {
  await axios.get("/api/ocupaciones_consultar_select").then((response) => {
    ocupacion.value = response.data;
  });
};

const obtenerTipoDocumento = async () => {
  await axios.get("/api/tipo_documentos_consultar_select").then((response) => {
    tipo_documento.value = response.data;
  });
};

const obtenerDireccion = async () => {
  await axios.get("/api/direccion_consultar_select").then((response) => {
    direccion.value = response.data;
  });
};

const obtenerPuesto = async () => {
  await axios.get("/api/puestos_consultar_select").then((response) => {
    puesto.value = response.data;
  });
};

const actualizar_selects = async () => {
  await obtenerEstadoCivil();
  await obtenerGeneros();
  await obtenerOcupacion();
  await obtenerTipoDocumento();
  await obtenerDireccion();
  await obtenerPuesto();
};
</script>
