<template>
    <q-layout>
        <q-page-container>
            <q-page class="flex fondo flex-center">
                <q-card v-bind:style="$q.screen.lt.sm ? { 'width': '80%' } : { 'width': '35%' }">
                    <q-card-section>
                        <q-avatar size="103px" class="absolute-center shadow-10" color="white">
                            <!--img src="profile.svg"-->
                            <img src="/imagenes/paymaster.svg" alt="Logo Paymaster">
                        </q-avatar>
                    </q-card-section>
                    <slot />
                </q-card>
            </q-page>
        </q-page-container>
    </q-layout>
</template>

<style>
.fondo {
    background-image: linear-gradient(135deg, #4d5f98 0%, #9f3669 100%);
        /*background: linear-gradient(to right, #4d5f98, #9f3669) !important;*/

}
</style>
