<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import Card from '@/Components/Card.vue';
import { Head, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
</script>

<template>
    <Head title="Início" />

    <AppLayout>
        <h2 class="text-xl font-semibold text-rosa-900">Olá, {{ user.name }}!</h2>
        <p class="mb-5 text-sm text-rosa-700">Sua saúde em dia é o melhor plano.</p>

        <div class="space-y-3">
            <Card icon="calendar" href="/lembretes">Seu próximo exame</Card>
            <Card icon="info" href="/informacoes">Informações e prevenção</Card>
            <Alert>A prevenção salva vidas!</Alert>
        </div>
    </AppLayout>
</template>
