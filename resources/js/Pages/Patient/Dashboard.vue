<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    overdueCount: { type: Number, required: true },
    nextReminder: { type: Object, default: null },
    lastCycle: { type: Object, default: null },
    preventive: { type: Object, default: null },
});

const firstName = usePage().props.auth.user.name.split(' ')[0];

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');
const formatDateTime = (local) => new Date(local).toLocaleString('pt-BR', { dateStyle: 'long', timeStyle: 'short' });

const badge = {
    baixo_risco: 'bg-green-100 text-green-800',
    atencao: 'bg-amber-100 text-amber-800',
    alta_prioridade: 'bg-red-100 text-red-800',
};
</script>

<template>
    <Head title="Início" />

    <AppLayout>
        <h2 class="text-xl font-semibold text-rosa-900">Olá, {{ firstName }}!</h2>
        <p class="mb-5 text-sm text-rosa-700">Sua saúde em dia é o melhor plano.</p>

        <div class="space-y-3">
            <Link
                v-if="overdueCount > 0"
                :href="route('reminders.index')"
                class="flex items-center justify-between rounded-2xl border border-red-300 bg-red-50 p-4 text-sm font-medium text-red-800 shadow-card"
            >
                {{ overdueCount === 1 ? 'Você tem 1 lembrete vencido' : `Você tem ${overdueCount} lembretes vencidos` }}
                <span class="underline">Ver</span>
            </Link>

            <Card icon="calendar" :href="route('reminders.index')">
                <template v-if="nextReminder">
                    <span class="text-xs text-rosa-700">Próximo lembrete</span>
                    <span class="block font-semibold text-rosa-900">{{ nextReminder.titulo }}</span>
                    <span class="block text-xs text-rosa-700">
                        {{ formatDateTime(nextReminder.data_hora) }} · {{ nextReminder.relative }}
                    </span>
                    <span
                        v-if="nextReminder.status === 'proximo'"
                        class="mt-1 inline-block rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800"
                    >
                        Próximo
                    </span>
                </template>
                <template v-else>
                    <span class="font-semibold text-rosa-900">Lembretes</span>
                    <span class="block text-xs text-rosa-700">Nenhum lembrete pendente. Adicione um.</span>
                </template>
            </Card>

            <Card icon="heart" :href="route('cycle.index')">
                <span class="text-xs text-rosa-700">Último ciclo registrado</span>
                <span v-if="lastCycle" class="block font-semibold text-rosa-900">
                    {{ formatDate(lastCycle.data_inicio) }}
                    <template v-if="lastCycle.data_fim">a {{ formatDate(lastCycle.data_fim) }}</template>
                    <template v-else>(em andamento)</template>
                </span>
                <span v-else class="block text-xs text-rosa-700">Nenhum registro ainda. Anote o início do seu ciclo.</span>
            </Card>

            <Card icon="exams" :href="route('exams.index')">
                <span class="text-xs text-rosa-700">Situação do preventivo</span>
                <template v-if="preventive">
                    <span
                        class="mt-0.5 inline-block rounded-full px-2 py-0.5 text-xs font-semibold"
                        :class="badge[preventive.risk]"
                    >
                        {{ preventive.risk_label }}
                    </span>
                    <span class="block text-xs text-rosa-700">
                        <template v-if="preventive.last_preventive">
                            Último exame: {{ formatDate(preventive.last_preventive) }}
                        </template>
                        <template v-else>Sem exame preventivo informado</template>
                    </span>
                </template>
                <span v-else class="block text-xs text-rosa-700">Responda o questionário de saúde para ver.</span>
            </Card>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <Card icon="heart" :href="route('questionnaires.create')">Questionário de saúde</Card>
                <Card icon="info" :href="route('info.index')">Informações e prevenção</Card>
                <Card icon="chat" :href="route('conversations.index')">Fale com um médico</Card>
            </div>
        </div>
    </AppLayout>
</template>
