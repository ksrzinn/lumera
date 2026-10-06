<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    topic: { type: Object, required: true },
    topics: { type: Array, required: true },
});
</script>

<template>
    <Head :title="topic.title" />

    <AppLayout :title="topic.title" :back="route('info.index')">
        <p class="mb-5 text-sm text-rosa-800">{{ topic.intro }}</p>

        <article class="space-y-4">
            <section
                v-for="section in topic.sections"
                :key="section.heading"
                class="rounded-2xl bg-white/80 p-4 shadow-card"
            >
                <h2 class="mb-2 font-semibold text-rosa-700">{{ section.heading }}</h2>
                <p v-if="section.text" class="text-sm leading-relaxed text-rosa-900">
                    {{ section.text }}
                </p>
                <ul v-if="section.items" class="list-disc space-y-1 ps-5 text-sm text-rosa-900">
                    <li v-for="item in section.items" :key="item">{{ item }}</li>
                </ul>
            </section>
        </article>

        <Alert class="mt-5">
            Conteúdo educativo. Não é diagnóstico e não substitui a avaliação de um profissional de saúde.
        </Alert>

        <section class="mt-8" aria-labelledby="outros-temas">
            <h2 id="outros-temas" class="mb-3 font-semibold text-rosa-700">Outros temas</h2>
            <ul class="space-y-2">
                <li v-for="other in topics.filter((t) => t.slug !== topic.slug)" :key="other.slug">
                    <Card :icon="other.icon" :href="route('info.show', other.slug)">
                        {{ other.title }}
                    </Card>
                </li>
            </ul>
        </section>

        <section class="mt-8 text-xs text-rosa-800">
            <h2 class="mb-1 font-semibold">Fontes</h2>
            <ul class="space-y-1">
                <li v-for="source in topic.sources" :key="source.url">
                    <a :href="source.url" target="_blank" rel="noopener" class="underline">
                        {{ source.label }}
                    </a>
                </li>
            </ul>
        </section>
    </AppLayout>
</template>
