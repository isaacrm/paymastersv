<template>
    <AppLayout title="Dashboard">
        <div class="q-pa-md">
            <q-card class="my-card">
                <q-card-section class="ml-6">
                    <div class="text-h6 text-center">Universidad de El Salvador</div>
                    <div class="text-h7 text-center">Ingeniería de Sistemas Informáticos</div>
                    <div class="text-h7 text-center">BAD115 - Base de Datos</div>
                    <div class="q-pa-sm">
                        <div class="q-gutter-sm text-center">
                            <img src="/imagenes/paymaster.svg" alt="Logo Paymaster" class="responsive-img-dashboard">
                        </div>
                    </div>
                    <div class="text-h6 text-center">BIENVENIDO A PAYMASTER SV</div>
                    <div class="text-h7 text-center">Paymaster tiene por objetivo la emisión de planillas de pago 
                        para empleados, así como el registro y administración de información vital tanto 
                        para la empresa como para los trabajadores. 
                    </div>
                    <div class="text-h7 text-center">Permite gestionar roles y permisos,
                        así como la información necesaria para mantener al día la información fiscal y contable 
                        de la empresa, y así aplicar los descuentos vigentes.
                    </div>
                </q-card-section>
                <q-card-section>
                    <div class="text-h7 text-weight-bold text-center">Integrantes</div>
                    <div class="text-h7 text-center">CC15043 - Jennifer Tatiana Cruz Castro</div>
                    <div class="text-h7 text-center">CH16034 - Katherine Gabriela Cárdenas Hernández</div>
                    <div class="text-h7 text-center">MV18037 - Rosalio Alfredo Monterrosa Valle</div>
                    <div class="text-h7 text-center">PR15033 - Moisés Alexander Portillo Ramos</div>
                    <div class="text-h7 text-center">RM16034 - Jorge Isaac Rodríguez Méndez</div>

                </q-card-section>
            </q-card>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue';
    import '/resources/css/paymaster.css'; // Ruta a tu archivo CSS personalizado

</script>
