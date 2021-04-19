<template>
    <inertia-link :href="href" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: {
            href: {
                type: String,
                default: '#',
            },
        }
    }
</script>
