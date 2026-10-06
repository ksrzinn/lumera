<script setup>
defineProps({
    type: { type: String, default: 'submit' },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex w-full items-center justify-center rounded-full bg-rosa-500 px-6 py-3 text-sm font-semibold text-white shadow-card transition hover:bg-rosa-600 focus:outline-none focus:ring-2 focus:ring-rosa-400 focus:ring-offset-2 focus:ring-offset-rosa-100 disabled:opacity-50"
    >
        <slot />
    </button>
</template>
