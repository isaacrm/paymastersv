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
    background-image: linear-gradient(135deg, #0416d7 0%, #ffffff 100%);
}
</style>
