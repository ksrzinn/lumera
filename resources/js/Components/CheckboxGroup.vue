<script setup>
const model = defineModel({ type: Array, default: () => [] });

const props = defineProps({
    options: { type: Object, required: true },
    // Opções que não combinam com as demais (ex.: "nenhum", "prefiro não responder").
    exclusive: { type: Array, default: () => [] },
});

const toggle = (value, checked) => {
    if (!checked) {
        model.value = model.value.filter((item) => item !== value);
    } else if (props.exclusive.includes(value)) {
        model.value = [value];
    } else {
        model.value = [...model.value.filter((item) => !props.exclusive.includes(item)), value];
    }
};
</script>

<template>
    <div class="space-y-2" role="group">
        <label
            v-for="(label, value) in options"
            :key="value"
            class="flex cursor-pointer items-center gap-3 rounded-xl border bg-white px-4 py-3 text-sm shadow-sm"
            :class="model.includes(value) ? 'border-rosa-500 text-rosa-800' : 'border-rosa-200 text-rosa-900'"
        >
            <input
                type="checkbox"
                :value="value"
                :checked="model.includes(value)"
                class="rounded border-rosa-300 text-rosa-500 focus:ring-rosa-400"
                @change="toggle(value, $event.target.checked)"
            />
            {{ label }}
        </label>
    </div>
</template>
