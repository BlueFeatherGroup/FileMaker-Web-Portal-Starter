<template>
    <inertia-link :href="href" :class="classes">
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                            ? 'inline-flex items-center px-1 pt-1  text-sm font-medium leading-5 text-blue-500 focus:outline-none focus:border-blue-700 transition'
                            : 'inline-flex items-center px-1 pt-1  text-sm font-medium leading-5 text-blue-500 hover:text-blue-400 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition'
            }
        }
    }
</script>
