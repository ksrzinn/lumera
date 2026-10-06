<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    items: { type: Array, required: true },
});

const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('pt-BR');

const badge = {
    baixo_risco: 'bg-green-100 text-green-800',
    atencao: 'bg-amber-100 text-amber-800',
    alta_prioridade: 'bg-red-100 text-red-800',
};
</script>

<template>
    <Head title="Histórico de saúde" />

    <AppLayout title="Histórico de saúde" :back="route('profile.edit')">
        <p v-if="items.length === 0" class="text-sm text-rosa-800">
            Seu histórico aparece aqui depois que você responder o questionário ou registrar um ciclo.
        </p>

        <ul class="space-y-3">
            <li v-for="item in items" :key="item.key">
                <Card :icon="item.kind === 'cycle' ? 'heart' : 'exams'" :href="item.href">
                    <span class="text-xs text-rosa-700">{{ formatDate(item.date) }}</span>
                    <span class="block font-medium text-rosa-900">{{ item.title }}</span>
                    <span
                        v-if="item.risk"
                        class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-semibold"
                        :class="badge[item.risk]"
                    >
                        {{ item.detail }}
                    </span>
                    <span v-else class="block text-xs text-rosa-700">{{ item.detail }}</span>
                </Card>
            </li>
        </ul>
    </AppLayout>
</template>
