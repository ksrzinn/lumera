<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    entries: { type: Array, required: true },
    flowOptions: { type: Object, required: true },
    symptomOptions: { type: Object, required: true },
});

const parse = (iso) => new Date(`${iso}T00:00:00`);
const formatDate = (iso) => parse(iso).toLocaleDateString('pt-BR');

const duration = (entry) =>
    entry.data_fim ? Math.round((parse(entry.data_fim) - parse(entry.data_inicio)) / 86400000) + 1 : null;

const groups = computed(() => {
    const byMonth = new Map();
    for (const entry of props.entries) {
        const label = parse(entry.data_inicio).toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });
        byMonth.set(label, [...(byMonth.get(label) ?? []), entry]);
    }
    return [...byMonth].map(([label, entries]) => ({ label, entries }));
});

const remove = (entry) => {
    if (confirm('Excluir este registro?')) {
        router.delete(route('cycle.destroy', entry.id));
    }
};
</script>

<template>
    <Head title="Ciclo menstrual" />

    <AppLayout title="Ciclo menstrual" :back="route('patient.dashboard')">
        <Link
            :href="route('cycle.create')"
            class="mb-6 block rounded-full bg-rosa-500 px-6 py-3 text-center text-sm font-semibold text-white shadow-card hover:bg-rosa-600"
        >
            + Novo registro
        </Link>

        <p v-if="entries.length === 0" class="text-sm text-rosa-800">
            Você ainda não tem registros. Anote o início do seu ciclo para acompanhá-lo.
        </p>

        <section v-for="group in groups" :key="group.label" class="mb-6">
            <h2 class="mb-2 text-sm font-semibold capitalize text-rosa-700">{{ group.label }}</h2>
            <ul class="space-y-3">
                <li v-for="entry in group.entries" :key="entry.id" class="rounded-2xl bg-white/80 p-4 shadow-card">
                    <div class="flex items-start justify-between gap-3">
                        <div class="text-sm">
                            <p class="font-medium text-rosa-900">
                                {{ formatDate(entry.data_inicio) }}
                                <template v-if="entry.data_fim">a {{ formatDate(entry.data_fim) }}</template>
                                <span v-else class="text-rosa-600">(em andamento)</span>
                            </p>
                            <p class="text-xs text-rosa-700">
                                <template v-if="duration(entry)">{{ duration(entry) }} dia(s)</template>
                                <template v-if="entry.fluxo"> · Fluxo {{ flowOptions[entry.fluxo].toLowerCase() }}</template>
                            </p>
                        </div>
                        <div class="flex shrink-0 gap-3 text-xs font-semibold">
                            <Link :href="route('cycle.edit', entry.id)" class="text-rosa-600 underline">Editar</Link>
                            <button type="button" class="text-red-600 underline" @click="remove(entry)">Excluir</button>
                        </div>
                    </div>

                    <ul v-if="entry.sintomas.length" class="mt-2 flex flex-wrap gap-1">
                        <li
                            v-for="symptom in entry.sintomas"
                            :key="symptom"
                            class="rounded-full bg-rosa-100 px-2 py-0.5 text-xs text-rosa-800"
                        >
                            {{ symptomOptions[symptom] }}
                        </li>
                    </ul>
                    <p v-if="entry.notas" class="mt-2 text-xs text-rosa-800">{{ entry.notas }}</p>
                </li>
            </ul>
        </section>
    </AppLayout>
</template>
