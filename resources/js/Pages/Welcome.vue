<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BrandLogo from '@/Components/BrandLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Início" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-rosa-100 px-6 text-center">
        <BrandLogo size="lg" />
        <p class="mt-3 text-sm text-rosa-600">Hoje pelo seu amanhã</p>

        <p class="mt-10 max-w-xs text-sm text-rosa-700">
            Rastreamento e prevenção do câncer do colo do útero
        </p>

        <div class="mt-10 flex w-full max-w-xs flex-col gap-3">
            <Link
                v-if="canLogin"
                :href="route('login')"
                class="rounded-full bg-rosa-500 px-6 py-3 text-sm font-semibold text-white shadow-card hover:bg-rosa-600"
            >
                Entrar
            </Link>
            <Link
                v-if="canRegister"
                :href="route('register')"
                class="rounded-full border border-rosa-400 bg-white/60 px-6 py-3 text-sm font-semibold text-rosa-600 hover:bg-white"
            >
                Criar conta
            </Link>
        </div>
    </div>
</template>
