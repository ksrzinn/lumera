<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    reminders: { type: Array, required: true },
    typeOptions: { type: Object, required: true },
});

const filter = ref('todos');
const filters = computed(() => ({ todos: 'Todos', ...props.typeOptions }));

const visible = computed(() =>
    filter.value === 'todos' ? props.reminders : props.reminders.filter((r) => r.tipo === filter.value),
);

const cardStyle = {
    vencido: 'border-red-300 bg-red-50',
    proximo: 'border-amber-300 bg-amber-50',
    futuro: 'border-transparent bg-white/80',
    concluido: 'border-transparent bg-white/50',
};

const statusLabel = { vencido: 'Vencido', proximo: 'Próximo' };
const statusBadge = { vencido: 'bg-red-100 text-red-800', proximo: 'bg-amber-100 text-amber-800' };

const formatDateTime = (local) =>
    new Date(local).toLocaleString('pt-BR', { dateStyle: 'long', timeStyle: 'short' });

const toggle = (reminder) => router.patch(route('reminders.toggle', reminder.id), {}, { preserveScroll: true });

const remove = (reminder) => {
    if (confirm('Excluir este lembrete?')) {
        router.delete(route('reminders.destroy', reminder.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Lembretes" />

    <AppLayout title="Lembretes" :back="route('patient.dashboard')">
        <div class="mb-5 flex gap-2 overflow-x-auto" role="tablist" aria-label="Filtrar por tipo">
            <button
                v-for="(label, key) in filters"
                :key="key"
                type="button"
                role="tab"
                :aria-selected="filter === key"
                class="shrink-0 rounded-full px-4 py-1.5 text-sm font-medium"
                :class="filter === key ? 'bg-rosa-500 text-white' : 'bg-rosa-200 text-rosa-700'"
                @click="filter = key"
            >
                {{ label }}
            </button>
        </div>

        <p v-if="visible.length === 0" class="mb-4 text-sm text-rosa-800">Nenhum lembrete por aqui.</p>

        <ul class="space-y-3">
            <li
                v-for="reminder in visible"
                :key="reminder.id"
                class="flex items-start gap-3 rounded-2xl border p-4 shadow-card"
                :class="cardStyle[reminder.status]"
            >
                <button
                    type="button"
                    class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2"
                    :class="reminder.concluido ? 'border-green-500 bg-green-500 text-white' : 'border-rosa-300 bg-white'"
                    :aria-label="reminder.concluido ? 'Marcar como pendente' : 'Marcar como concluído'"
                    @click="toggle(reminder)"
                >
                    <Icon v-if="reminder.concluido" name="check" class="h-3.5 w-3.5" />
                </button>

                <div class="min-w-0 flex-1 text-sm">
                    <p class="font-medium text-rosa-900" :class="{ 'line-through opacity-60': reminder.concluido }">
                        {{ reminder.titulo }}
                    </p>
                    <p class="text-xs text-rosa-700">
                        {{ formatDateTime(reminder.data_hora) }} · {{ reminder.relative }}
                    </p>
                    <div class="mt-1 flex items-center gap-2 text-xs">
                        <span class="text-rosa-600">{{ typeOptions[reminder.tipo] }}</span>
                        <span
                            v-if="statusLabel[reminder.status]"
                            class="rounded-full px-2 py-0.5 font-semibold"
                            :class="statusBadge[reminder.status]"
                        >
                            {{ statusLabel[reminder.status] }}
                        </span>
                        <span v-if="reminder.concluido" class="font-semibold text-green-700">Concluído</span>
                    </div>
                </div>

                <div class="flex shrink-0 gap-3 text-xs font-semibold">
                    <Link :href="route('reminders.edit', reminder.id)" class="text-rosa-600 underline">Editar</Link>
                    <button type="button" class="text-red-600 underline" @click="remove(reminder)">Excluir</button>
                </div>
            </li>
        </ul>

        <Link
            :href="route('reminders.create')"
            class="mt-6 block rounded-full bg-rosa-500 px-6 py-3 text-center text-sm font-semibold text-white shadow-card hover:bg-rosa-600"
        >
            + Adicionar lembrete
        </Link>
    </AppLayout>
</template>
