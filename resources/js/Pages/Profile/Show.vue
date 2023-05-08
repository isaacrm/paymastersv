<template>
    <AppLayout title="Perfil">
        <div v-if="$page.props.jetstream.canUpdateProfileInformation" class="q-pa-md">
            <UpdateProfileInformationForm :user="$page.props.auth.user" />
        </div>

        <div v-if="$page.props.jetstream.canUpdatePassword" class="q-pa-md">
            <UpdatePasswordForm />
        </div>

        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="q-pa-md">
            <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
        </div>
        <div class="q-pa-md">
            <LogoutOtherBrowserSessionsForm :sessions="sessions" />
        </div>

        <div v-if="$page.props.jetstream.hasAccountDeletionFeatures" class="q-pa-md">
            <DeleteUserForm />
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>
