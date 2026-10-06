<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    dataNascimento: { type: String, default: null },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    data_nascimento: props.dataNascimento ?? '',
});
</script>

<template>
    <section>
        <h2 class="text-lg font-semibold text-rosa-800">Dados pessoais</h2>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-4 space-y-4">
            <div>
                <InputLabel for="name" value="Nome" />
                <TextInput id="name" type="text" class="mt-1" v-model="form.name" required autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail" />
                <TextInput id="email" type="email" class="mt-1" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="data_nascimento" value="Data de nascimento" />
                <TextInput
                    id="data_nascimento"
                    type="date"
                    class="mt-1"
                    v-model="form.data_nascimento"
                    required
                    autocomplete="bday"
                />
                <InputError class="mt-2" :message="form.errors.data_nascimento" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton class="sm:w-auto" :disabled="form.processing">Salvar</PrimaryButton>
                <p v-if="form.recentlySuccessful" class="text-sm text-rosa-700">Salvo.</p>
            </div>
        </form>
    </section>
</template>
