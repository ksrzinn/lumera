<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <section class="space-y-4">
        <h2 class="text-lg font-semibold text-rosa-800">Excluir conta</h2>

        <p class="text-sm text-rosa-800">
            Ao excluir sua conta, todos os seus dados (questionários, ciclos e lembretes) são apagados de forma
            permanente.
        </p>

        <DangerButton @click="confirmUserDeletion">Excluir conta</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-rosa-800">Tem certeza que deseja excluir sua conta?</h2>

                <p class="mt-1 text-sm text-rosa-800">
                    Esta ação não pode ser desfeita. Digite sua senha para confirmar.
                </p>

                <div class="mt-6">
                    <InputLabel for="delete_password" value="Senha" class="sr-only" />
                    <TextInput
                        id="delete_password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        placeholder="Senha"
                        @keyup.enter="deleteUser"
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton class="!w-auto" @click="closeModal">Cancelar</SecondaryButton>
                    <DangerButton :disabled="form.processing" @click="deleteUser">Excluir conta</DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
