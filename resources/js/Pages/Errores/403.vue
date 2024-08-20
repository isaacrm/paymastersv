<template>
    <q-layout class="bg-image" v-cloak>
      <q-page-container>
        <q-page class="flex flex-center">
          <q-card class="bg-transparent no-border no-shadow">
            <div class="row items-center full-width justify-center q-col-gutter-lg">
              <q-icon name="block" color="bg-grey-2 text-white" size="5rem"></q-icon>
            </div>
            <div class="row full-width">
              <div class="col-lg-12 col-md-12 cl-sm-12 col-xs-12">
                <div>
                  <div class="text-h3 text-center text-white">
                    Acceso denegado
                  </div>
                  <div class="text-h6 text-center text-white">
                    Lo siento, no tienes los permisos necesarios para acceder a esta página.
                  </div>
                </div>
              </div>
            </div>
          </q-card>
        </q-page>
      </q-page-container>
    </q-layout>
  </template>
  
  <script>
    import {defineComponent} from 'vue'
  
    export default defineComponent({
    name: "Error403"
  })
  </script>
  
  
  <style>
  .bg-image {
    background-image: linear-gradient(135deg, #4d5f98 0%, #9f3669 100%);
  }
  
  [v-cloak] {
    display: none !important;
  }
  </style>