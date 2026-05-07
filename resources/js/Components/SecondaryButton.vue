<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 sm:px-6 sm:py-3 text-sm font-semibold text-gray-700 transition-all duration-200 shadow-sm hover:bg-gray-50 hover:shadow-md hover:-translate-y-0.5 active:bg-gray-100 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 disabled:cursor-not-allowed min-h-touch"
    >
        <slot />
    </button>
</template>
