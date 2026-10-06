<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    preventive: { type: Object, default: null },
});

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');

const badge = {
    baixo_risco: 'bg-green-100 text-green-800',
    atencao: 'bg-amber-100 text-amber-800',
    alta_prioridade: 'bg-red-100 text-red-800',
};
</script>

<template>
    <Head title="Meus exames" />

    <AppLayout title="Meus exames">
        <section class="mb-5 rounded-3xl bg-white/80 p-5 shadow-card">
            <h2 class="font-semibold text-rosa-900">Exame preventivo (Papanicolau)</h2>

            <template v-if="preventive">
                <span
                    class="mt-2 inline-block rounded-full px-3 py-1 text-sm font-semibold"
                    :class="badge[preventive.risk]"
                >
                    {{ preventive.risk_label }}
                </span>
                <p class="mt-2 text-sm text-rosa-800">
                    <template v-if="preventive.last_preventive">
                        Último exame informado: {{ formatDate(preventive.last_preventive) }}
                    </template>
                    <template v-else>Você informou que não fez ou não lembra do último exame.</template>
                </p>
                <p class="mt-1 text-xs text-rosa-700">
                    Com base no questionário de {{ formatDate(preventive.answered_at) }}. Orientação educativa, não
                    diagnóstico.
                </p>
            </template>
            <p v-else class="mt-2 text-sm text-rosa-800">
                Responda o questionário de saúde para ver a situação do seu preventivo.
            </p>
        </section>

        <div class="space-y-3">
            <Card icon="heart" :href="route('questionnaires.create')">
                {{ preventive ? 'Responder o questionário novamente' : 'Responder o questionário de saúde' }}
            </Card>
            <Card icon="exams" :href="route('history.index')">Meu histórico de saúde</Card>
            <Card icon="calendar" :href="route('reminders.index')">Lembretes e agendamentos</Card>
            <Card icon="info" :href="route('info.show', 'exames')">Saiba mais sobre os exames</Card>
        </div>
    </AppLayout>
</template>
