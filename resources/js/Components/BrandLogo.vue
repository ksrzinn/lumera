<script setup>
defineProps({
    size: { type: String, default: 'md' },
});

const sizes = { sm: 'text-2xl', md: 'text-4xl', lg: 'text-6xl' };
</script>

<template>
    <span class="inline-flex flex-col items-center leading-none text-rosa-500">
        <svg
            v-if="size === 'lg'"
            viewBox="0 0 24 24"
            class="mb-2 h-12 w-12"
            fill="currentColor"
            aria-hidden="true"
        >
            <path d="M12 20s-8-5-8-11a4.5 4.5 0 018-2.8A4.5 4.5 0 0120 9c0 6-8 11-8 11z" />
        </svg>
        <span class="font-script font-semibold" :class="sizes[size]">Cuidar</span>
    </span>
</template>
