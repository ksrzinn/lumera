<script setup>
import { levelBadge } from '@/lib/careLevel';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    preventive: { type: Object, default: null },
});

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');

</script>

<template>
    <Head title="Meus exames" />

    <AppLayout title="Meus exames">
        <section class="mb-5 rounded-3xl bg-white/80 p-5 shadow-card">
            <h2 class="font-semibold text-rosa-900">Situação da sua prevenção</h2>

            <template v-if="preventive">
                <span
                    class="mt-2 inline-block rounded-full px-3 py-1 text-sm font-semibold"
                    :class="levelBadge[preventive.level]"
                >
                    {{ preventive.level_label }}
                </span>
                <p class="mt-2 text-sm text-rosa-800">{{ preventive.exam_note }}</p>
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
