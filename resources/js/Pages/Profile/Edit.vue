<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    dataNascimento: { type: String, default: null },
});

const user = usePage().props.auth.user;
const isPatient = user.role === 'patient';
const initials = computed(() =>
    user.name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join(''),
);
</script>

<template>
    <Head title="Meu perfil" />

    <AppLayout title="Meu perfil">
        <div class="mb-6 flex flex-col items-center text-center">
            <span
                class="flex h-20 w-20 items-center justify-center rounded-full bg-rosa-300 text-2xl font-semibold text-white"
                aria-hidden="true"
            >
                {{ initials }}
            </span>
            <p class="mt-3 text-lg font-semibold text-rosa-900">{{ user.name }}</p>
            <p class="text-sm text-rosa-700">{{ user.email }}</p>
        </div>

        <ul v-if="isPatient" class="mb-6 space-y-3">
            <li><Card icon="exams" :href="route('history.index')">Histórico de saúde</Card></li>
            <li><Card icon="syringe" :href="route('info.show', 'hpv-e-vacinacao')">Vacinação HPV</Card></li>
        </ul>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white/80 p-4 shadow-card sm:p-6">
                <UpdateProfileInformationForm :data-nascimento="dataNascimento" />
            </div>

            <div class="rounded-2xl bg-white/80 p-4 shadow-card sm:p-6">
                <UpdatePasswordForm />
            </div>

            <div class="rounded-2xl bg-white/80 p-4 shadow-card sm:p-6">
                <DeleteUserForm />
            </div>
        </div>
    </AppLayout>
</template>
