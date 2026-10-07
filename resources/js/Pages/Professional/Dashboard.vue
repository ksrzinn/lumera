<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    pendingCount: { type: Number, required: true },
    pending: { type: Array, required: true },
    recent: { type: Array, required: true },
});

const name = usePage().props.auth.user.name;
</script>

<template>
    <Head title="Início" />

    <AppLayout>
        <h2 class="text-xl font-semibold text-rosa-900">Olá, {{ name }}!</h2>
        <p class="mb-5 text-sm text-rosa-700">Painel do profissional de saúde.</p>

        <section aria-labelledby="pendentes">
            <div class="mb-2 flex items-center justify-between">
                <h3 id="pendentes" class="font-semibold text-rosa-800">
                    Mensagens pendentes
                    <span
                        v-if="pendingCount > 0"
                        class="ms-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800"
                    >
                        {{ pendingCount }}
                    </span>
                </h3>
                <Link :href="route('conversations.index')" class="text-xs font-semibold text-rosa-600 underline">
                    Caixa de entrada
                </Link>
            </div>

            <p v-if="pending.length === 0" class="rounded-2xl bg-white/60 p-4 text-sm text-rosa-800">
                Nenhuma mensagem aguardando resposta.
            </p>

            <ul class="space-y-3">
                <li v-for="item in pending" :key="item.conversation_id">
                    <Card icon="chat" :href="route('conversations.show', item.conversation_id)">
                        <span class="block font-semibold text-rosa-900">{{ item.patient }}</span>
                        <span class="block truncate text-xs text-rosa-700">{{ item.assunto }}</span>
                        <span class="block truncate text-xs text-rosa-600">{{ item.last_message }}</span>
                    </Card>
                </li>
            </ul>
        </section>

        <section class="mt-8" aria-labelledby="recentes">
            <div class="mb-2 flex items-center justify-between">
                <h3 id="recentes" class="font-semibold text-rosa-800">Pacientes recentes</h3>
                <Link :href="route('patients.index')" class="text-xs font-semibold text-rosa-600 underline">
                    Ver todos
                </Link>
            </div>

            <p v-if="recent.length === 0" class="rounded-2xl bg-white/60 p-4 text-sm text-rosa-800">
                Nenhuma paciente enviou mensagem ainda.
            </p>

            <ul class="space-y-3">
                <li v-for="item in recent" :key="item.conversation_id">
                    <Card icon="user" :href="route('conversations.show', item.conversation_id)">
                        <span class="block font-semibold text-rosa-900">{{ item.patient }}</span>
                        <span class="block truncate text-xs text-rosa-700">{{ item.assunto }}</span>
                    </Card>
                </li>
            </ul>
        </section>
    </AppLayout>
</template>
