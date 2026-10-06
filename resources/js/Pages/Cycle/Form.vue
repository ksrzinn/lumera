<script setup>
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RadioGroup from '@/Components/RadioGroup.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    entry: { type: Object, default: null },
    flowOptions: { type: Object, required: true },
    symptomOptions: { type: Object, required: true },
});

const form = useForm({
    data_inicio: props.entry?.data_inicio ?? '',
    data_fim: props.entry?.data_fim ?? '',
    fluxo: props.entry?.fluxo ?? '',
    sintomas: props.entry?.sintomas ?? [],
    notas: props.entry?.notas ?? '',
});

const today = new Date().toISOString().slice(0, 10);

const hasWarningSymptom = computed(() => form.sintomas.some((s) => s !== 'nenhum'));

const toggleSymptom = (key) => {
    if (form.sintomas.includes(key)) {
        form.sintomas = form.sintomas.filter((s) => s !== key);
    } else if (key === 'nenhum') {
        form.sintomas = ['nenhum'];
    } else {
        form.sintomas = [...form.sintomas.filter((s) => s !== 'nenhum'), key];
    }
};

const submit = () => {
    if (props.entry) {
        form.put(route('cycle.update', props.entry.id));
    } else {
        form.post(route('cycle.store'));
    }
};
</script>

<template>
    <Head :title="entry ? 'Editar registro' : 'Novo registro'" />

    <AppLayout :title="entry ? 'Editar registro' : 'Novo registro'" :back="route('cycle.index')">
        <form @submit.prevent="submit" class="space-y-5">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <InputLabel for="data_inicio" value="Início" />
                    <TextInput id="data_inicio" type="date" class="mt-1" :max="today" v-model="form.data_inicio" required />
                    <InputError class="mt-2" :message="form.errors.data_inicio" />
                </div>
                <div>
                    <InputLabel for="data_fim" value="Fim" />
                    <TextInput id="data_fim" type="date" class="mt-1" :min="form.data_inicio" :max="today" v-model="form.data_fim" />
                    <InputError class="mt-2" :message="form.errors.data_fim" />
                </div>
            </div>

            <div>
                <InputLabel value="Fluxo" />
                <div class="mt-1">
                    <RadioGroup v-model="form.fluxo" name="fluxo" :options="flowOptions" />
                </div>
                <InputError class="mt-2" :message="form.errors.fluxo" />
            </div>

            <fieldset>
                <legend class="text-base font-semibold text-rosa-900">Como você está se sentindo?</legend>
                <p class="mb-3 text-sm text-rosa-800">Selecione os sintomas que está sentindo (se houver).</p>
                <div class="space-y-2">
                    <label
                        v-for="(label, key) in symptomOptions"
                        :key="key"
                        class="flex cursor-pointer items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm shadow-sm"
                        :class="form.sintomas.includes(key) ? 'border-rosa-500' : 'border-rosa-200'"
                    >
                        <input
                            type="checkbox"
                            :checked="form.sintomas.includes(key)"
                            class="rounded border-rosa-300 text-rosa-500 focus:ring-rosa-400"
                            @change="toggleSymptom(key)"
                        />
                        {{ label }}
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.sintomas" />
            </fieldset>

            <Alert v-if="hasWarningSymptom" variant="warning">
                <!-- TODO: revisar (orientação clínica) -->
                Esses sinais não são um diagnóstico e podem ter várias causas. Procure uma unidade de saúde para
                ser avaliada.
            </Alert>

            <div>
                <InputLabel for="notas" value="Notas" />
                <textarea
                    id="notas"
                    v-model="form.notas"
                    rows="3"
                    maxlength="1000"
                    class="mt-1 w-full rounded-xl border-rosa-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-rosa-400 focus:ring-rosa-400"
                />
                <InputError class="mt-2" :message="form.errors.notas" />
            </div>

            <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>
        </form>
    </AppLayout>
</template>
