<template>
  <Head title="Welcome" />
  <q-layout
    view="lHh Lpr lFf"
    style="background: linear-gradient(135deg, #4d5f98ff 10%, #9f3669ff 100%)"
  >
    <q-header class="bg-transparent text-white">
      <q-toolbar class="q-pa-md">
        <q-toolbar-title> Paymaster SV </q-toolbar-title>
        <q-space />

        <div v-if="canLogin" class="q-gutter-sm row items-center no-wrap">
          <q-btn
            square
            dense
            flat
            color="text-grey-7"
            label="Dashboard"
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            ><q-tooltip>Dashboard</q-tooltip></q-btn
          >

          <q-btn
            square
            dense
            flat
            color="text-grey-7"
            label="Iniciar Sesión"
            v-else
            :href="route('login')"
            ><q-tooltip>Iniciar Sesión</q-tooltip></q-btn
          >

          <q-btn
            square
            dense
            flat
            color="text-grey-7"
            label="Registrarse"
            v-if="canRegister"
            :href="route('register')"
            ><q-tooltip>Registrarse</q-tooltip></q-btn
          >
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <section
        style="min-height: 25vh"
        class="flex text-white flex-center layout_bg"
      >
        <div style="position: relative">
          <div class="text-h4 text-center">PAYMASTER SV</div>
          <div class="text-subtitle2 q-pt-sm text-center">
            Ofrece las siguientes características
          </div>
        </div>
      </section>
      <section class="q-pb-lg">
        <div class="row q-col-gutter-sm q-px-sm">
          <div class="col-lg-3 col-md-3 col-xs-12 col-sm-12">
            <q-card
              class="text-white"
              style="
                background: linear-gradient(to right, #ed6ea0 0%, #ec8c69 100%);
              "
            >
              <q-card-section>
                <div class="text-h6 text-center">Registrar empleados</div>
              </q-card-section>
              <q-card-section>
                <div class="text-h2 text-weight-bolder text-center">
                  <q-icon name="groups"></q-icon>
                </div>
              </q-card-section>
              <q-card-section>
                <div class="text-h6 text-weight-bolder text-center">
                  Permite la gestión precisa de empleados y la administración de los datos personales 
                  como nombres, apellidos, documentos personales, salario, puesto de trabajo, entre otros
                </div>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-lg-3 col-md-3 col-xs-12 col-sm-12">
            <q-card
              class="text-white"
              style="
                background: linear-gradient(-225deg, #5d9fff 0%, #6bbbff 100%);
              "
            >
              <q-card-section>
                <div class="text-h6 text-center">Guardar configuracion</div>
              </q-card-section>
              <q-card-section>
                <div class="text-h2 text-weight-bolder text-center">
                  <q-icon name="settings"></q-icon>
                </div>
              </q-card-section>
              <q-card-section>
                <div class="text-h6 text-weight-bolder text-center">
                  Brinda la administración de información compelmentaria como direcciones, departamentos y municipios, generos,
                  estados civiles y ocupaciones
                </div>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-lg-3 col-md-3 col-xs-12 col-sm-12">
            <q-card
              class="text-white"
              style="
                background: linear-gradient(to right, #2b86c5 0%, #2b86c5 100%);
              "
            >
              <q-card-section>
                <div class="text-h6 text-center">
                  Configuracion de la institucion
                </div>
              </q-card-section>
              <q-card-section>
                <div class="text-h2 text-weight-bolder text-center">
                  <q-icon name="corporate_fare"></q-icon>
                </div>
              </q-card-section>
              <q-card-section>
                <div class="text-h6 text-weight-bolder text-center">
                  Permite mantener una jerarquía ante el registro de puestos y unidades de una empresa, 
                  así como el detalle del centro de costos y planillas de
                  de la organización
                </div>
              </q-card-section>
            </q-card>
          </div>
          <div class="col-lg-3 col-md-3 col-xs-12 col-sm-12">
            <q-card
              class="text-white"
              style="
                background: linear-gradient(
                  87deg,
                  rgb(17, 205, 239),
                  rgb(17, 113, 239)
                ) !important;
              "
            >
              <q-card-section>
                <div class="text-h6 text-center">Registros al detalle</div>
              </q-card-section>
              <q-card-section>
                <div class="text-h2 text-weight-bolder text-center">
                  <q-icon name="manage_accounts"></q-icon>
                </div>
              </q-card-section>
              <q-card-section>
                <div class="text-h6 text-weight-bolder text-center">
                  Ofrece el registro especificos de salario, horas trabajadas, descuentos aplicados,
                  ingresos obtenidos, por cada empleado perteneciente a la organización
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </section>
    </q-page-container>

    <section class="flex row flex-center q-py-sm text-center">
      <div class="text-weight-bold text-subtitle2 text-white">
        <div>Copyright © {{ year }} Paymaster SV</div>
        <div>Desarrollado por: Grupo 06 - BAD115 - UES</div>
      </div>
    </section>
  </q-layout>
</template>

<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
});

const year = new Date().getFullYear();
</script>
