<script setup>
defineProps({
    name: { type: String, required: true },
});

const paths = {
    home: 'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10',
    exams: 'M7 3h8l4 4v14H7zM15 3v4h4M10 12h6M10 16h6',
    chat: 'M21 12a8 8 0 01-11.6 7.1L4 20l1.1-4.6A8 8 0 1121 12z',
    user: 'M12 12a4 4 0 100-8 4 4 0 000 8zM4 21a8 8 0 0116 0',
    bell: 'M6 9a6 6 0 1112 0c0 6 2 7 2 7H4s2-1 2-7zM10 20h4',
    calendar: 'M4 6h16v14H4zM4 10h16M8 3v4M16 3v4',
    heart: 'M12 20s-8-5-8-11a4.5 4.5 0 018-2.8A4.5 4.5 0 0120 9c0 6-8 11-8 11z',
    info: 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 11v5M12 8h.01',
    pin: 'M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11zM12 12a2 2 0 100-4 2 2 0 000 4z',
    check: 'M5 12l5 5 9-10',
    'chevron-right': 'M9 5l7 7-7 7',
    'arrow-left': 'M19 12H5M11 5l-7 7 7 7',
    logout: 'M10 4H5v16h5M15 8l4 4-4 4M19 12H9',
};
</script>

<template>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <path :d="paths[name]" />
    </svg>
</template>
