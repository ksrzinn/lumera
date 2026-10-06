<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    professionals: { type: Object, required: true },
});

const form = useForm({
    professional_id: '',
    assunto: '',
    corpo: '',
});
</script>

<template>
    <Head title="Nova conversa" />

    <AppLayout title="Nova conversa" :back="route('conversations.index')">
        <Alert class="mb-5">
            O chat é para tirar dúvidas e não serve para emergências. Em caso de urgência, procure um serviço de
            saúde.
        </Alert>

        <p v-if="Object.keys(professionals).length === 0" class="text-sm text-rosa-800">
            Ainda não há profissionais cadastrados.
        </p>

        <form v-else @submit.prevent="form.post(route('conversations.store'))" class="space-y-5">
            <div>
                <InputLabel for="professional_id" value="Profissional" />
                <div class="mt-1">
                    <SelectInput id="professional_id" v-model="form.professional_id" :options="professionals" />
                </div>
                <InputError class="mt-2" :message="form.errors.professional_id" />
            </div>

            <div>
                <InputLabel for="assunto" value="Assunto" />
                <TextInput id="assunto" type="text" class="mt-1" maxlength="120" v-model="form.assunto" required />
                <InputError class="mt-2" :message="form.errors.assunto" />
            </div>

            <div>
                <InputLabel for="corpo" value="Mensagem" />
                <textarea
                    id="corpo"
                    v-model="form.corpo"
                    rows="4"
                    maxlength="2000"
                    required
                    class="mt-1 w-full rounded-xl border-rosa-200 bg-white px-4 py-3 text-sm shadow-sm focus:border-rosa-400 focus:ring-rosa-400"
                />
                <InputError class="mt-2" :message="form.errors.corpo" />
            </div>

            <PrimaryButton :disabled="form.processing">Enviar</PrimaryButton>
        </form>
    </AppLayout>
</template>
