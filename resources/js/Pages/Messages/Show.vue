<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import { Head, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    conversation: { type: Object, required: true },
    messages: { type: Array, required: true },
});

const POLL_INTERVAL_MS = 5000;

const form = useForm({ corpo: '' });
const list = ref(null);
let timer = null;

const scrollToEnd = () => nextTick(() => list.value?.scrollTo({ top: list.value.scrollHeight }));

const poll = () => {
    if (document.visibilityState === 'visible' && !form.processing) {
        router.reload({ only: ['conversation', 'messages'] });
    }
};

const send = () => {
    form.post(route('messages.store', props.conversation.id), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const close = () => {
    if (confirm('Encerrar esta conversa? Ninguém poderá enviar novas mensagens.')) {
        router.patch(route('conversations.close', props.conversation.id));
    }
};

onMounted(() => {
    scrollToEnd();
    timer = setInterval(poll, POLL_INTERVAL_MS);
});

onBeforeUnmount(() => clearInterval(timer));

watch(() => props.messages.length, scrollToEnd);
</script>

<template>
    <Head :title="conversation.with" />

    <AppLayout :title="conversation.with" :back="route('conversations.index')">
        <div class="mb-3 flex items-center justify-between gap-3 text-xs text-rosa-700">
            <span class="truncate">{{ conversation.assunto }}</span>
            <button v-if="conversation.can_close" type="button" class="shrink-0 font-semibold text-red-600 underline" @click="close">
                Encerrar conversa
            </button>
        </div>

        <div
            ref="list"
            class="h-[55vh] space-y-2 overflow-y-auto rounded-2xl bg-white/50 p-3"
            aria-live="polite"
        >
            <div
                v-for="message in messages"
                :key="message.id"
                class="flex"
                :class="message.mine ? 'justify-end' : 'justify-start'"
            >
                <div
                    class="max-w-[80%] rounded-2xl px-3 py-2 text-sm shadow-sm"
                    :class="message.mine ? 'bg-rosa-300 text-rosa-900' : 'bg-white text-rosa-900'"
                >
                    <p class="whitespace-pre-wrap break-words">{{ message.corpo }}</p>
                    <p class="mt-1 text-right text-[10px] text-rosa-700">{{ message.sent_at }}</p>
                </div>
            </div>
        </div>

        <p v-if="conversation.status === 'closed'" class="mt-3 text-center text-sm text-rosa-700">
            Esta conversa foi encerrada.
        </p>

        <form v-else @submit.prevent="send" class="mt-3">
            <div class="flex items-center gap-2">
                <input
                    v-model="form.corpo"
                    type="text"
                    maxlength="2000"
                    placeholder="Digite sua mensagem..."
                    aria-label="Mensagem"
                    class="w-full rounded-full border-rosa-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-rosa-400 focus:ring-rosa-400"
                />
                <button
                    type="submit"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rosa-500 text-white hover:bg-rosa-600 disabled:opacity-50"
                    :disabled="form.processing || form.corpo.trim() === ''"
                    aria-label="Enviar"
                >
                    <Icon name="send" class="h-5 w-5" />
                </button>
            </div>
            <InputError class="mt-2" :message="form.errors.corpo" />
        </form>
    </AppLayout>
</template>
