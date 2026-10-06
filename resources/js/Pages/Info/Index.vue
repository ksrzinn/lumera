<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    topics: { type: Array, required: true },
});
</script>

<template>
    <Head title="Informações" />

    <AppLayout title="Informações">
        <div class="mb-5 rounded-3xl bg-rosa-300/60 p-6">
            <p class="text-lg font-semibold text-rosa-900">
                Conheça o câncer do colo do útero
            </p>
            <p class="mt-1 text-sm text-rosa-800">
                Informação confiável para cuidar de você.
            </p>
        </div>

        <ul class="space-y-3">
            <li v-for="topic in topics" :key="topic.slug">
                <Card :icon="topic.icon" :href="route('info.show', topic.slug)">
                    <span class="font-medium text-rosa-900">{{ topic.title }}</span>
                </Card>
            </li>
        </ul>
    </AppLayout>
</template>
