<template>
    <q-layout title="Error">
      <q-layout class="bg-image" v-cloak>
        <q-page-container>
          <q-page class="flex flex-center">
            <q-card class="bg-transparent no-border no-shadow">
              <div class="row items-center full-width justify-center q-col-gutter-lg">
                <q-icon name="error_outline" color="bg-grey-2 text-white" size="5rem"></q-icon>
              </div>
              <div class="row full-width">
                <div class="col-lg-12 col-md-12 cl-sm-12 col-xs-12">
                  <div>
                    <div class="text-h3 text-center text-white">
                      ¡Lo sentimos! 
                    </div>
                    <div class="text-h6 text-center text-white">
                      {{ message }}
                    </div>
                    <div class="text-h6 text-center text-white">
                        Podrás intentarlo nuevamente en hasta que el administrador active tu cuenta.
                    </div>
                  </div>
                </div>
              </div>
            </q-card>
          </q-page>
        </q-page-container>
      </q-layout>
    </q-layout>
  </template>
  
  <script>
  export default {
    name: "Error429",
    props: {
      message: String,
      //retry_after: String
    }
  };
  </script>
  
  <style>
  .bg-image {
    background-image: linear-gradient(135deg, #4d5f98 0%, #9f3669 100%);
  }
  
  [v-cloak] {
    display: none !important;
  }
  </style>
  