<script setup>
import { Link } from '@inertiajs/vue3';
import BrandLogo from '@/Components/BrandLogo.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-rosa-100 px-4 py-10 sm:justify-center">
        <Link href="/" aria-label="Cuidar">
            <BrandLogo size="md" />
        </Link>

        <div class="mt-8 w-full max-w-md rounded-3xl bg-white/80 p-6 shadow-card">
            <slot />
        </div>

        <p class="mt-8 text-xs text-rosa-700/80">Sua saúde importa.</p>
    </div>
</template>
