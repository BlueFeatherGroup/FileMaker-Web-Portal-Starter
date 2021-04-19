<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links">
                <!-- inactive button -->
                <div v-if="link.url === null" :key="key"
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label"/>
                <!-- Active Button -->
                <div v-else @click="$emit('clicked', link.label)"
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded bg-white hover:bg-blue-500 focus:border-blue-300 hover:text-white focus:text-white cursor-pointer"
                     :class="{
                    'bg-blue-500': link.active,
                    'text-white': link.active,
                    }" v-html="link.label"/>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    emits: ['clicked'],
    props: {
        links: Array,
        currentPage: Number,
    },
};
</script>
