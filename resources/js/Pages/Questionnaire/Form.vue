<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CheckboxGroup from '@/Components/CheckboxGroup.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RadioGroup from '@/Components/RadioGroup.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    sections: { type: Array, required: true },
});

const QUESTIONS_PER_STEP = 4;

// Numeração fixa pela ordem da definição, para casar com o roteiro do questionário.
let counter = 0;
const numbered = props.sections.map((section) => ({
    ...section,
    questions: section.questions.map((question) => ({ ...question, number: ++counter })),
}));

// Seções longas são divididas em etapas de tamanho parecido.
const steps = numbered.flatMap((section) => {
    const parts = Math.ceil(section.questions.length / QUESTIONS_PER_STEP);
    const size = Math.ceil(section.questions.length / parts);

    return Array.from({ length: parts }, (_, i) => ({
        title: section.title,
        questions: section.questions.slice(i * size, (i + 1) * size),
    }));
});

const form = useForm(
    Object.fromEntries(
        numbered.flatMap((section) => section.questions).map((q) => [q.field, q.type === 'checkbox' ? [] : '']),
    ),
);

const step = ref(0);
const total = steps.length;

const isVisible = (question) => !question.show_if || form[question.show_if.field] === question.show_if.equals;
const visibleQuestions = computed(() => steps[step.value].questions.filter(isVisible));

const isAnswered = (question) =>
    question.type === 'checkbox' ? form[question.field].length > 0 : form[question.field] !== '';

const stepIsAnswered = computed(() => visibleQuestions.value.every(isAnswered));

const stepWithError = computed(() => {
    const fields = Object.keys(form.errors).map((key) => key.split('.')[0]);
    const indexes = steps
        .map((s, index) => (s.questions.some((q) => fields.includes(q.field)) ? index : null))
        .filter((index) => index !== null);

    return indexes.length ? Math.min(...indexes) : null;
});

const next = () => {
    if (step.value < total - 1) {
        // pergunta oculta não guarda resposta
        steps[step.value].questions
            .filter((q) => !isVisible(q))
            .forEach((q) => (form[q.field] = q.type === 'checkbox' ? [] : ''));
        step.value++;
        window.scrollTo({ top: 0 });
        return;
    }

    form.post(route('questionnaires.store'), {
        onError: () => {
            step.value = stepWithError.value ?? step.value;
        },
    });
};
</script>

<template>
    <Head title="Questionário de saúde" />

    <AppLayout title="Questionário de saúde" :back="route('patient.dashboard')">
        <div class="mb-6">
            <div class="mb-1 flex justify-between text-xs text-rosa-700">
                <span>{{ steps[step].title }}</span>
                <span>{{ step + 1 }} de {{ total }}</span>
            </div>
            <div class="h-1.5 rounded-full bg-rosa-200">
                <div
                    class="h-1.5 rounded-full bg-rosa-500 transition-all"
                    :style="{ width: `${((step + 1) / total) * 100}%` }"
                />
            </div>
        </div>

        <form @submit.prevent="next" class="space-y-8">
            <div v-for="question in visibleQuestions" :key="question.field" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">{{ question.number }}. {{ question.label }}</h2>

                <CheckboxGroup
                    v-if="question.type === 'checkbox'"
                    v-model="form[question.field]"
                    :options="question.options"
                    :exclusive="question.exclusive ?? []"
                />
                <RadioGroup v-else v-model="form[question.field]" :name="question.field" :options="question.options" />

                <InputError :message="form.errors[question.field]" />
            </div>

            <PrimaryButton :disabled="!stepIsAnswered || form.processing">
                {{ step === total - 1 ? 'Ver resultado' : 'Próximo' }}
            </PrimaryButton>
        </form>
    </AppLayout>
</template>
