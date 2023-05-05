<template>
    <Head title="Welcome" />
    <q-layout view="lHh lpr lFf" container style="height: 100vh" class="shadow-2 rounded-borders">
        <q-header reveal elevated>
            <q-toolbar>
                <q-toolbar-title>Paymaster SV</q-toolbar-title>
                <div v-if="canLogin">
                    <q-btn flat dense v-if="$page.props.auth.user" :href="route('dashboard')"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Dashboard</q-btn>

                    <q-btn flat dense v-else :href="route('login')"
                        class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Log
                        in</q-btn>

                    <q-btn flat dense v-if="canRegister" :href="route('register')"
                        class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Register</q-btn>
                </div>
            </q-toolbar>
        </q-header>
        <q-page-container>
            <div>
                <h1 class="text-center">
                    Banner
                </h1>
            </div>
        </q-page-container>

        <q-footer reveal elevated class="text-center q-pa-lg letter-spacing-100">
            <div>
                Copyright © 2023 PAYMASTER SV
            </div>
            <div>
                Desarrollado por: Grupo 06 - BAD115 - UES
            </div>
        </q-footer>

    </q-layout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const backgroundImgUrl = ref('')
backgroundImgUrl.value = 'https://example.com/my-background-image.jpg'
</script>
