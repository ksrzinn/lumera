<script setup>
const model = defineModel({ type: [String, Number], default: '' });

defineProps({
    options: { type: Object, required: true },
    placeholder: { type: String, default: 'Selecione' },
});
</script>

<template>
    <select
        v-model="model"
        class="w-full rounded-xl border-rosa-200 bg-white px-4 py-3 text-sm text-rosa-900 shadow-sm focus:border-rosa-400 focus:ring-rosa-400"
    >
        <option value="" disabled>{{ placeholder }}</option>
        <option v-for="(label, value) in options" :key="value" :value="value">{{ label }}</option>
    </select>
</template>
