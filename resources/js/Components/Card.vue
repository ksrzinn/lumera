<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

defineProps({
    href: { type: String, default: null },
    icon: { type: String, default: null },
});
</script>

<template>
    <component
        :is="href ? Link : 'div'"
        :href="href"
        class="flex items-center gap-4 rounded-2xl bg-white/80 p-4 shadow-card"
        :class="href && 'transition hover:bg-white'"
    >
        <span
            v-if="icon"
            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rosa-100 text-rosa-500"
        >
            <Icon :name="icon" class="h-5 w-5" />
        </span>
        <div class="min-w-0 flex-1 text-sm"><slot /></div>
        <Icon v-if="href" name="chevron-right" class="h-4 w-4 shrink-0 text-rosa-400" />
    </component>
</template>
