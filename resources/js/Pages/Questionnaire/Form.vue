<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RadioGroup from '@/Components/RadioGroup.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    options: { type: Object, required: true },
});

const ages = Object.fromEntries(Array.from({ length: 112 }, (_, i) => [String(i + 9), `${i + 9} anos`]));

const form = useForm({
    idade: '',
    fez_preventivo: '',
    data_ultimo_preventivo: '',
    usa_camisinha: '',
    metodo_contraceptivo: '',
    vacinada_hpv: '',
});

const step = ref(1);
const total = 5;
const today = new Date().toISOString().slice(0, 10);

const stepIsAnswered = computed(() => {
    switch (step.value) {
        case 1:
            return form.idade !== '';
        case 2:
            return form.fez_preventivo !== '' && (form.fez_preventivo !== 'sim' || form.data_ultimo_preventivo !== '');
        case 3:
            return form.usa_camisinha !== '';
        case 4:
            return form.metodo_contraceptivo !== '';
        default:
            return form.vacinada_hpv !== '';
    }
});

const stepWithError = computed(() => {
    const fieldStep = { idade: 1, fez_preventivo: 2, data_ultimo_preventivo: 2, usa_camisinha: 3, metodo_contraceptivo: 4, vacinada_hpv: 5 };
    const steps = Object.keys(form.errors).map((field) => fieldStep[field]);
    return steps.length ? Math.min(...steps) : null;
});

const next = () => {
    if (step.value < total) {
        step.value++;
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
            <div class="mb-1 flex justify-end text-xs text-rosa-700">{{ step }} de {{ total }}</div>
            <div class="h-1.5 rounded-full bg-rosa-200">
                <div
                    class="h-1.5 rounded-full bg-rosa-500 transition-all"
                    :style="{ width: `${(step / total) * 100}%` }"
                />
            </div>
        </div>

        <form @submit.prevent="next" class="space-y-6">
            <div v-if="step === 1">
                <h2 class="mb-3 font-semibold text-rosa-900">Qual a sua idade?</h2>
                <SelectInput v-model="form.idade" :options="ages" />
                <InputError class="mt-2" :message="form.errors.idade" />
            </div>

            <div v-else-if="step === 2" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você já fez o exame preventivo (Papanicolau)?</h2>
                <RadioGroup v-model="form.fez_preventivo" name="fez_preventivo" :options="props.options.preventivo" />
                <InputError :message="form.errors.fez_preventivo" />

                <div v-if="form.fez_preventivo === 'sim'" class="pt-2">
                    <label for="data_ultimo_preventivo" class="mb-1 block text-sm font-medium text-rosa-800">
                        Quando foi o último?
                    </label>
                    <TextInput id="data_ultimo_preventivo" type="date" :max="today" v-model="form.data_ultimo_preventivo" />
                    <InputError class="mt-2" :message="form.errors.data_ultimo_preventivo" />
                </div>
            </div>

            <div v-else-if="step === 3" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você usa camisinha nas relações sexuais?</h2>
                <RadioGroup v-model="form.usa_camisinha" name="usa_camisinha" :options="props.options.camisinha" />
                <InputError :message="form.errors.usa_camisinha" />
            </div>

            <div v-else-if="step === 4">
                <h2 class="mb-3 font-semibold text-rosa-900">Qual método contraceptivo você usa?</h2>
                <SelectInput v-model="form.metodo_contraceptivo" :options="props.options.contraceptivo" />
                <InputError class="mt-2" :message="form.errors.metodo_contraceptivo" />
            </div>

            <div v-else class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você tomou a vacina contra o HPV?</h2>
                <RadioGroup v-model="form.vacinada_hpv" name="vacinada_hpv" :options="props.options.vacina" />
                <InputError :message="form.errors.vacinada_hpv" />
            </div>

            <PrimaryButton :disabled="!stepIsAnswered || form.processing">
                {{ step === total ? 'Ver resultado' : 'Próximo' }}
            </PrimaryButton>
        </form>
    </AppLayout>
</template>
