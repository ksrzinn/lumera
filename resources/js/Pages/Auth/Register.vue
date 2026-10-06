<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Icon from '@/Components/Icon.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    role: 'patient',
    name: '',
    email: '',
    data_nascimento: '',
    password: '',
    password_confirmation: '',
    consent: false,
});

const roles = [
    { value: 'patient', title: 'Sou paciente', text: 'Acompanhe seus exames, receba lembretes e cuide da sua saúde.' },
    { value: 'professional', title: 'Sou profissional de saúde', text: 'Acesse as informações e converse com seus pacientes.' },
];

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar conta" />

        <h1 class="mb-4 text-xl font-bold text-rosa-600">Escolha o seu acesso</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <fieldset class="space-y-3">
                <legend class="sr-only">Perfil</legend>
                <label
                    v-for="role in roles"
                    :key="role.value"
                    class="flex cursor-pointer items-center gap-3 rounded-2xl border-2 bg-white p-3 shadow-card"
                    :class="form.role === role.value ? 'border-rosa-500' : 'border-transparent'"
                >
                    <input type="radio" class="sr-only" name="role" :value="role.value" v-model="form.role" />
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rosa-100 text-rosa-500">
                        <Icon name="user" class="h-5 w-5" />
                    </span>
                    <span class="text-sm">
                        <span class="block font-semibold text-rosa-600">{{ role.title }}</span>
                        <span class="text-xs text-rosa-700">{{ role.text }}</span>
                    </span>
                </label>
                <InputError :message="form.errors.role" />
            </fieldset>

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
                <TextInput id="data_nascimento" type="date" class="mt-1" v-model="form.data_nascimento" required autocomplete="bday" />
                <InputError class="mt-2" :message="form.errors.data_nascimento" />
            </div>

            <div>
                <InputLabel for="password" value="Senha" />
                <TextInput id="password" type="password" class="mt-1" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel for="password_confirmation" value="Confirmar senha" />
                <TextInput id="password_confirmation" type="password" class="mt-1" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div>
                <label class="flex items-start gap-2">
                    <Checkbox name="consent" v-model:checked="form.consent" class="mt-0.5" />
                    <span class="text-xs text-rosa-800">
                        Li e concordo com o tratamento dos meus dados pessoais e de saúde para o uso
                        do aplicativo, conforme a Lei Geral de Proteção de Dados (LGPD).
                    </span>
                </label>
                <InputError class="mt-2" :message="form.errors.consent" />
            </div>

            <PrimaryButton :disabled="form.processing">Criar conta</PrimaryButton>

            <p class="text-center text-sm text-rosa-700">
                Já tem conta?
                <Link :href="route('login')" class="font-semibold text-rosa-600 underline">Entrar</Link>
            </p>
        </form>
    </GuestLayout>
</template>
