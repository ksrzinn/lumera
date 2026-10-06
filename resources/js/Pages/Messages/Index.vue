<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    conversations: { type: Array, required: true },
});

const isPatient = usePage().props.auth.user.role === 'patient';
</script>

<template>
    <Head title="Mensagens" />

    <AppLayout :title="isPatient ? 'Fale com um médico' : 'Caixa de entrada'">
        <Link
            v-if="isPatient"
            :href="route('conversations.create')"
            class="mb-6 block rounded-full bg-rosa-500 px-6 py-3 text-center text-sm font-semibold text-white shadow-card hover:bg-rosa-600"
        >
            + Nova conversa
        </Link>

        <p v-if="conversations.length === 0" class="text-sm text-rosa-800">
            {{ isPatient ? 'Você ainda não iniciou nenhuma conversa.' : 'Nenhuma conversa por enquanto.' }}
        </p>

        <ul class="space-y-3">
            <li v-for="conversation in conversations" :key="conversation.id">
                <Card icon="chat" :href="route('conversations.show', conversation.id)">
                    <div class="flex items-center gap-2">
                        <span class="truncate font-semibold text-rosa-900">{{ conversation.with }}</span>
                        <span
                            v-if="conversation.awaiting_reply"
                            class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800"
                        >
                            Aguardando resposta
                        </span>
                        <span
                            v-else-if="conversation.status === 'closed'"
                            class="shrink-0 rounded-full bg-rosa-200 px-2 py-0.5 text-xs font-semibold text-rosa-700"
                        >
                            Encerrada
                        </span>
                    </div>
                    <span class="block truncate text-xs text-rosa-700">{{ conversation.assunto }}</span>
                    <span v-if="conversation.last_message" class="block truncate text-xs text-rosa-600">
                        {{ conversation.last_message }}
                    </span>
                </Card>
            </li>
        </ul>
    </AppLayout>
</template>
