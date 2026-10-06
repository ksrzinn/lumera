<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Início" />

    <AppLayout>
        <h2 class="text-xl font-semibold text-rosa-900">Olá, {{ user.name }}!</h2>
        <p class="text-sm text-rosa-700">Painel do profissional de saúde.</p>
    </AppLayout>
</template>
