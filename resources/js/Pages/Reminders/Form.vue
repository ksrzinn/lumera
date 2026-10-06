<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RadioGroup from '@/Components/RadioGroup.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    reminder: { type: Object, default: null },
    typeOptions: { type: Object, required: true },
});

const form = useForm({
    titulo: props.reminder?.titulo ?? '',
    data_hora: props.reminder?.data_hora ?? '',
    tipo: props.reminder?.tipo ?? 'preventivo',
});

const submit = () => {
    if (props.reminder) {
        form.put(route('reminders.update', props.reminder.id));
    } else {
        form.post(route('reminders.store'));
    }
};
</script>

<template>
    <Head :title="reminder ? 'Editar lembrete' : 'Novo lembrete'" />

    <AppLayout :title="reminder ? 'Editar lembrete' : 'Novo lembrete'" :back="route('reminders.index')">
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="titulo" value="Título" />
                <TextInput id="titulo" type="text" class="mt-1" maxlength="120" v-model="form.titulo" required autofocus />
                <InputError class="mt-2" :message="form.errors.titulo" />
            </div>

            <div>
                <InputLabel for="data_hora" value="Data e hora" />
                <TextInput id="data_hora" type="datetime-local" class="mt-1" v-model="form.data_hora" required />
                <InputError class="mt-2" :message="form.errors.data_hora" />
            </div>

            <div>
                <InputLabel value="Tipo" />
                <div class="mt-1">
                    <RadioGroup v-model="form.tipo" name="tipo" :options="typeOptions" />
                </div>
                <InputError class="mt-2" :message="form.errors.tipo" />
            </div>

            <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>
        </form>
    </AppLayout>
</template>
