<script setup>
defineProps({
    type: { type: String, default: 'button' },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex w-full items-center justify-center rounded-full border border-rosa-400 bg-white/60 px-6 py-3 text-sm font-semibold text-rosa-600 transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-rosa-400 focus:ring-offset-2 focus:ring-offset-rosa-100 disabled:opacity-50"
    >
        <slot />
    </button>
</template>
