<script setup>
import { levelBadge } from '@/lib/careLevel';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    questionnaire: { type: Object, required: true },
    analysis: { type: Object, required: true },
});

const urgentCode = 'sintoma_sangramento_intenso_com_mal_estar';

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Resultado do questionário" />

    <AppLayout title="Resultado" :back="route('patient.dashboard')">
        <template v-if="analysis.urgent">
            <Alert variant="danger" class="mb-5">
                <p class="font-semibold">Procure atendimento de urgência</p>
                <p class="mt-1">
                    {{ analysis.guidance.find((item) => item.code === urgentCode)?.guidance }}
                </p>
            </Alert>
        </template>

        <section class="rounded-3xl bg-white/80 p-6 shadow-card">
            <p class="text-xs text-rosa-700">Respondido em {{ formatDate(questionnaire.created_at) }}</p>
            <span class="mt-2 inline-block rounded-full px-3 py-1 text-sm font-semibold" :class="levelBadge[analysis.level]">
                {{ analysis.label }}
            </span>
            <p class="mt-4 text-sm leading-relaxed text-rosa-900">{{ analysis.summary }}</p>
        </section>

        <section v-if="analysis.attention_points.length" class="mt-5 rounded-3xl bg-white/80 p-6 shadow-card">
            <h2 class="font-semibold text-rosa-900">Pontos que merecem atenção</h2>
            <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-rosa-900">
                <li v-for="point in analysis.attention_points" :key="point.code">{{ point.title }}</li>
            </ul>
        </section>

        <section class="mt-5 rounded-3xl bg-white/80 p-6 shadow-card">
            <h2 class="font-semibold text-rosa-900">Orientações para você</h2>
            <div class="mt-3 space-y-4">
                <div v-for="item in analysis.guidance" :key="item.code">
                    <h3 class="text-sm font-medium text-rosa-800">{{ item.title }}</h3>
                    <p class="mt-1 text-sm leading-relaxed text-rosa-900">{{ item.guidance }}</p>
                </div>
            </div>
        </section>

        <section class="mt-5 rounded-3xl bg-white/80 p-6 shadow-card">
            <h2 class="font-semibold text-rosa-900">Próximos passos</h2>
            <p class="mt-2 text-sm leading-relaxed text-rosa-900">{{ analysis.follow_up }}</p>
        </section>

        <Alert class="mt-5">
            Este resultado é uma orientação educativa, não um diagnóstico. Ele não classifica seu risco clínico nem
            substitui a consulta com um profissional de saúde.
        </Alert>

        <div class="mt-6 space-y-3">
            <Card icon="exams" :href="route('questionnaires.index')">Ver histórico de questionários</Card>
            <Card icon="info" :href="route('info.index')">Saiba mais sobre prevenção</Card>
        </div>
    </AppLayout>
</template>
