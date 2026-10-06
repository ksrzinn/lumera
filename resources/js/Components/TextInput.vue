<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full rounded-xl border-rosa-200 bg-white px-4 py-3 text-sm text-rosa-900 shadow-sm placeholder:text-rosa-300 focus:border-rosa-400 focus:ring-rosa-400"
        v-model="model"
        ref="input"
    />
</template>
