<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import CheckboxGroup from '@/Components/CheckboxGroup.vue';
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
    historico_alteracao: '',
    acompanhamento_alteracao: '',
    usa_camisinha: '',
    metodo_contraceptivo: '',
    vacinada_hpv: '',
    sintomas: [],
    barreiras: [],
});

const step = ref(1);
const total = 8;
const today = new Date().toISOString().slice(0, 10);

const stepIsAnswered = computed(() => {
    switch (step.value) {
        case 1:
            return form.idade !== '';
        case 2:
            return form.fez_preventivo !== '';
        case 3:
            return form.historico_alteracao !== '' && (form.historico_alteracao !== 'sim' || form.acompanhamento_alteracao !== '');
        case 4:
            return form.usa_camisinha !== '';
        case 5:
            return form.metodo_contraceptivo !== '';
        case 6:
            return form.vacinada_hpv !== '';
        case 7:
            return form.sintomas.length > 0;
        default:
            return form.barreiras.length > 0;
    }
});

const stepWithError = computed(() => {
    const fieldStep = {
        idade: 1,
        fez_preventivo: 2,
        data_ultimo_preventivo: 2,
        historico_alteracao: 3,
        acompanhamento_alteracao: 3,
        usa_camisinha: 4,
        metodo_contraceptivo: 5,
        vacinada_hpv: 6,
        sintomas: 7,
        barreiras: 8,
    };
    const steps = Object.keys(form.errors).map((field) => fieldStep[field.split('.')[0]]);
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
                        Quando foi o último? (se não souber a data, deixe em branco)
                    </label>
                    <TextInput id="data_ultimo_preventivo" type="date" :max="today" v-model="form.data_ultimo_preventivo" />
                    <InputError class="mt-2" :message="form.errors.data_ultimo_preventivo" />
                </div>
            </div>

            <div v-else-if="step === 3" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">
                    Você já teve diagnóstico de HPV, lesão no colo do útero ou alteração em exame preventivo?
                </h2>
                <RadioGroup
                    v-model="form.historico_alteracao"
                    name="historico_alteracao"
                    :options="props.options.historico_alteracao"
                />
                <InputError :message="form.errors.historico_alteracao" />

                <div v-if="form.historico_alteracao === 'sim'" class="space-y-3 pt-2">
                    <h3 class="text-sm font-medium text-rosa-800">Você fez o acompanhamento recomendado pela equipe de saúde?</h3>
                    <RadioGroup
                        v-model="form.acompanhamento_alteracao"
                        name="acompanhamento_alteracao"
                        :options="props.options.acompanhamento"
                    />
                    <InputError :message="form.errors.acompanhamento_alteracao" />
                </div>
            </div>

            <div v-else-if="step === 4" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você usa camisinha nas relações sexuais?</h2>
                <RadioGroup v-model="form.usa_camisinha" name="usa_camisinha" :options="props.options.camisinha" />
                <InputError :message="form.errors.usa_camisinha" />
            </div>

            <div v-else-if="step === 5">
                <h2 class="mb-3 font-semibold text-rosa-900">Qual método contraceptivo você usa?</h2>
                <SelectInput v-model="form.metodo_contraceptivo" :options="props.options.contraceptivo" />
                <InputError class="mt-2" :message="form.errors.metodo_contraceptivo" />
            </div>

            <div v-else-if="step === 6" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você tomou a vacina contra o HPV?</h2>
                <RadioGroup v-model="form.vacinada_hpv" name="vacinada_hpv" :options="props.options.vacina" />
                <InputError :message="form.errors.vacinada_hpv" />
            </div>

            <div v-else-if="step === 7" class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Você tem ou teve algum destes sintomas recentemente?</h2>
                <CheckboxGroup
                    v-model="form.sintomas"
                    :options="props.options.sintomas"
                    :exclusive="['nenhum', 'prefiro_nao_responder']"
                />
                <InputError :message="form.errors.sintomas" />
            </div>

            <div v-else class="space-y-3">
                <h2 class="font-semibold text-rosa-900">Algo dificulta você de fazer exames ou ir ao serviço de saúde?</h2>
                <CheckboxGroup v-model="form.barreiras" :options="props.options.barreiras" :exclusive="['nenhuma']" />
                <InputError :message="form.errors.barreiras" />
            </div>

            <PrimaryButton :disabled="!stepIsAnswered || form.processing">
                {{ step === total ? 'Ver resultado' : 'Próximo' }}
            </PrimaryButton>
        </form>
    </AppLayout>
</template>
