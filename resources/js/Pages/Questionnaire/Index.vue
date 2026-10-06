<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    items: { type: Array, required: true },
});

const badge = {
    baixo_risco: 'bg-green-100 text-green-800',
    atencao: 'bg-amber-100 text-amber-800',
    alta_prioridade: 'bg-red-100 text-red-800',
};

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');
</script>

<template>
    <Head title="Histórico do questionário" />

    <AppLayout title="Histórico" :back="route('patient.dashboard')">
        <p v-if="items.length === 0" class="mb-4 text-sm text-rosa-800">
            Você ainda não respondeu o questionário de saúde.
        </p>

        <ul class="space-y-3">
            <li v-for="item in items" :key="item.id">
                <Card icon="exams" :href="route('questionnaires.show', item.id)">
                    <span class="block font-medium text-rosa-900">{{ formatDate(item.created_at) }}</span>
                    <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold" :class="badge[item.risk]">
                        {{ item.risk_label }}
                    </span>
                </Card>
            </li>
        </ul>

        <Link
            :href="route('questionnaires.create')"
            class="mt-6 block rounded-full bg-rosa-500 px-6 py-3 text-center text-sm font-semibold text-white shadow-card hover:bg-rosa-600"
        >
            Responder novamente
        </Link>
    </AppLayout>
</template>
