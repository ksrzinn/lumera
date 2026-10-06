<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    patients: { type: Array, required: true },
});
</script>

<template>
    <Head title="Pacientes" />

    <AppLayout title="Pacientes" :back="route('professional.dashboard')">
        <p v-if="patients.length === 0" class="text-sm text-rosa-800">
            Nenhuma paciente enviou mensagem ainda.
        </p>

        <ul class="space-y-3">
            <li v-for="patient in patients" :key="patient.id">
                <Card icon="user" :href="route('conversations.show', patient.open_conversation_id ?? patient.last_conversation_id)">
                    <span class="block font-semibold text-rosa-900">{{ patient.name }}</span>
                    <span class="block text-xs text-rosa-700">{{ patient.email }}</span>
                    <span class="block text-xs text-rosa-600">
                        {{ patient.conversations === 1 ? '1 conversa' : `${patient.conversations} conversas` }}
                        <template v-if="patient.open_conversation_id"> · aberta</template>
                    </span>
                </Card>
            </li>
        </ul>
    </AppLayout>
</template>
