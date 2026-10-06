<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'info' },
});

const styles = {
    info: 'border-rosa-200 bg-rosa-50 text-rosa-800',
    success: 'border-green-200 bg-green-50 text-green-800',
    warning: 'border-amber-200 bg-amber-50 text-amber-800',
    danger: 'border-red-200 bg-red-50 text-red-800',
};

const classes = computed(() => styles[props.variant] ?? styles.info);
</script>

<template>
    <div role="alert" class="rounded-xl border px-4 py-3 text-sm" :class="classes">
        <slot />
    </div>
</template>
