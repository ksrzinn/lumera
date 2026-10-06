<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    questionnaire: { type: Object, required: true },
    risk: { type: Object, required: true },
});

const badge = {
    baixo_risco: 'bg-green-100 text-green-800',
    atencao: 'bg-amber-100 text-amber-800',
    alta_prioridade: 'bg-red-100 text-red-800',
};

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Resultado do questionário" />

    <AppLayout title="Resultado" :back="route('patient.dashboard')">
        <section class="rounded-3xl bg-white/80 p-6 shadow-card">
            <p class="text-xs text-rosa-700">Respondido em {{ formatDate(questionnaire.created_at) }}</p>
            <span class="mt-2 inline-block rounded-full px-3 py-1 text-sm font-semibold" :class="badge[risk.level]">
                {{ risk.label }}
            </span>
            <p class="mt-4 text-sm leading-relaxed text-rosa-900">{{ risk.orientation }}</p>
        </section>

        <Alert class="mt-5">
            Este resultado é uma orientação educativa, não um diagnóstico. Ele não substitui a consulta com um
            profissional de saúde.
        </Alert>

        <div class="mt-6 space-y-3">
            <Card icon="exams" :href="route('questionnaires.index')">Ver histórico de questionários</Card>
            <Card icon="info" :href="route('info.index')">Saiba mais sobre prevenção</Card>
        </div>
    </AppLayout>
</template>
