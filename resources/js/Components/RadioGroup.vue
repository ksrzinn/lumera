<script setup>
const model = defineModel({ type: String, default: '' });

defineProps({
    name: { type: String, required: true },
    options: { type: Object, required: true },
});
</script>

<template>
    <div class="space-y-2" role="radiogroup">
        <label
            v-for="(label, value) in options"
            :key="value"
            class="flex cursor-pointer items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm shadow-sm"
            :class="model === value ? 'border-rosa-500 text-rosa-800' : 'border-rosa-200 text-rosa-900'"
        >
            <input
                v-model="model"
                type="radio"
                :name="name"
                :value="value"
                class="border-rosa-300 text-rosa-500 focus:ring-rosa-400"
            />
            {{ label }}
        </label>
    </div>
</template>
